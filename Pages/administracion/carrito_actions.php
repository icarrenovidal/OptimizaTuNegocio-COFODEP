<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Forzar JSON
header('Content-Type: application/json; charset=utf-8');

include __DIR__ . '/../../Config/conexion.php'; // $conexion como MySQLi

// ------------------
// PARÁMETROS
// ------------------
$action = $_POST['action'] ?? $_GET['action'] ?? '';
$id_producto = isset($_POST['id_producto']) ? intval($_POST['id_producto']) : null;
$cantidad = isset($_POST['cantidad']) ? intval($_POST['cantidad']) : 1;
if ($cantidad < 1) $cantidad = 1;

// ------------------
// INICIALIZAR CARRITO EN SESIÓN
// ------------------
if (!isset($_SESSION['carrito'])) $_SESSION['carrito'] = [];

// ------------------
// BASE URL PARA IMÁGENES
// ------------------
$baseUrl = '/OptimizaTuNegocio/OptimizaTuNegocio/';

// ------------------
// FUNCIONES AUXILIARES
// ------------------
function obtenerProducto($conexion, $id_producto, $baseUrl)
{
    $sql = "
        SELECT 
            p.id_producto,
            p.nombre,
            COALESCE(pp.precio_venta, 0) AS precio,
            ip.ruta AS imagen
        FROM productos p
        LEFT JOIN precios_productos pp 
            ON pp.id_producto = p.id_producto 
            AND (pp.fecha_fin IS NULL OR pp.fecha_fin >= CURDATE())
        LEFT JOIN imagenes_productos ip 
            ON ip.id_producto = p.id_producto
        WHERE p.id_producto = ?
        ORDER BY ip.fecha_subida DESC, pp.fecha_inicio DESC
        LIMIT 1
    ";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id_producto);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $prod = $resultado->fetch_assoc();
    $stmt->close();

    if ($prod) {
        $prod['precio'] = floatval($prod['precio']);
        $prod['imagen'] = $prod['imagen']
            ? $baseUrl . ltrim($prod['imagen'], '/')
            : 'https://via.placeholder.com/80';
    }

    return $prod ?: null;
}

// ------------------
// ACCIONES
// ------------------

// 1️⃣ AGREGAR PRODUCTO (sumar cantidades si ya existe)
if ($action === 'add' && $id_producto) {
    $cantidad_solicitada = $cantidad;

    // --- 1. Obtener stock total disponible del producto ---
    $stmtStock = $conexion->prepare("SELECT SUM(cantidad_actual) as stock_total FROM lotes WHERE id_producto = ?");
    $stmtStock->bind_param("i", $id_producto);
    $stmtStock->execute();
    $resStock = $stmtStock->get_result()->fetch_assoc();
    $stock_total = (int)$resStock['stock_total'];
    $stmtStock->close();

    // --- 2. Obtener cantidad que ya tiene el usuario en el carrito ---
    $cantidad_en_carrito = 0;
    if (isset($_SESSION['id_usuario'])) {
        $id_usuario = $_SESSION['id_usuario'];
        $stmtC = $conexion->prepare("SELECT cantidad FROM carrito WHERE id_usuario=? AND id_producto=?");
        $stmtC->bind_param("ii", $id_usuario, $id_producto);
        $stmtC->execute();
        $resC = $stmtC->get_result()->fetch_assoc();
        $cantidad_en_carrito = $resC['cantidad'] ?? 0;
        $stmtC->close();
    } elseif (isset($_SESSION['carrito'][$id_producto])) {
        $cantidad_en_carrito = $_SESSION['carrito'][$id_producto];
    }

    // --- 3. Validar stock ---
    if ($cantidad_en_carrito + $cantidad_solicitada > $stock_total) {
        echo json_encode([
            'status' => 'error',
            'msg' => 'No hay suficiente stock disponible.'
        ]);
        exit;
    }

    // --- 4. Agregar al carrito sesión ---
    if (!isset($_SESSION['carrito'][$id_producto])) $_SESSION['carrito'][$id_producto] = 0;
    $_SESSION['carrito'][$id_producto] += $cantidad_solicitada;

    // --- 5. Agregar o actualizar en BD ---
    if (isset($_SESSION['id_usuario'])) {
        $stmt = $conexion->prepare("SELECT cantidad FROM carrito WHERE id_usuario=? AND id_producto=?");
        $stmt->bind_param("ii", $id_usuario, $id_producto);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows > 0) {
            $row = $resultado->fetch_assoc();
            $nueva_cant = $row['cantidad'] + $cantidad_solicitada;
            $stmt2 = $conexion->prepare("UPDATE carrito SET cantidad=? WHERE id_usuario=? AND id_producto=?");
            $stmt2->bind_param("iii", $nueva_cant, $id_usuario, $id_producto);
            $stmt2->execute();
            $stmt2->close();
        } else {
            $stmt2 = $conexion->prepare("INSERT INTO carrito(id_usuario, id_producto, cantidad) VALUES(?,?,?)");
            $stmt2->bind_param("iii", $id_usuario, $id_producto, $cantidad_solicitada);
            $stmt2->execute();
            $stmt2->close();
        }
        $stmt->close();
    }

    $total_carrito = array_sum($_SESSION['carrito']);
    echo json_encode([
        'status' => 'ok',
        'carrito' => $_SESSION['carrito'],
        'total_carrito' => $total_carrito
    ]);
    exit;
}

// 2️⃣ LISTAR CARRITO
if ($action === 'list') {
    $response = [];

    if (isset($_SESSION['id_usuario'])) {
        $id_usuario = $_SESSION['id_usuario'];
        $sql = "
            SELECT 
                c.id_producto, 
                c.cantidad, 
                p.nombre,
                COALESCE(pp.precio_venta,0) AS precio,
                ip.ruta AS imagen
            FROM carrito c
            JOIN productos p ON c.id_producto = p.id_producto
            LEFT JOIN precios_productos pp
                ON pp.id_producto = p.id_producto
                AND (pp.fecha_fin IS NULL OR pp.fecha_fin >= CURDATE())
            LEFT JOIN imagenes_productos ip
                ON ip.id_producto = p.id_producto
            WHERE c.id_usuario=?
            ORDER BY ip.fecha_subida DESC, pp.fecha_inicio DESC
        ";
        $stmt = $conexion->prepare($sql);
        if (!$stmt) {
            echo json_encode([
                'debug' => ['error_sql' => $conexion->error]
            ]);
            exit;
        }

        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        $resultado = $stmt->get_result();

        while ($prod = $resultado->fetch_assoc()) {
            $imgPath = $prod['imagen'] ? $baseUrl . ltrim($prod['imagen'], '/') : 'https://via.placeholder.com/80';
            $response[] = [
                'id_producto' => $prod['id_producto'],
                'nombre_producto' => $prod['nombre'],
                'precio' => floatval($prod['precio']),
                'cantidad' => intval($prod['cantidad']),
                'imagen' => $imgPath
            ];
        }
        $stmt->close();

        if (empty($response)) {
            echo json_encode([]);
            exit;
        }
    } else {
        if (!empty($_SESSION['carrito'])) {
            foreach ($_SESSION['carrito'] as $id => $cant) {
                $prod = obtenerProducto($conexion, $id, $baseUrl);
                if ($prod) {
                    $response[] = [
                        'id_producto' => $prod['id_producto'],
                        'nombre_producto' => $prod['nombre'],
                        'precio' => $prod['precio'],
                        'cantidad' => $cant,
                        'imagen' => $prod['imagen']
                    ];
                }
            }
        }
    }

    echo json_encode($response);
    exit;
}


// 3️⃣ ELIMINAR PRODUCTO
if ($action === 'remove' && $id_producto) {
    unset($_SESSION['carrito'][$id_producto]);

    if (isset($_SESSION['id_usuario'])) {
        $stmt = $conexion->prepare("DELETE FROM carrito WHERE id_usuario=? AND id_producto=?");
        $stmt->bind_param("ii", $_SESSION['id_usuario'], $id_producto);
        $stmt->execute();
        $stmt->close();
    }

    echo json_encode(['status' => 'ok']);
    exit;
}

// 4️⃣ VACÍAR CARRITO
if ($action === 'clear') {
    $_SESSION['carrito'] = [];

    if (isset($_SESSION['id_usuario'])) {
        $stmt = $conexion->prepare("DELETE FROM carrito WHERE id_usuario=?");
        $stmt->bind_param("i", $_SESSION['id_usuario']);
        $stmt->execute();
        $stmt->close();
    }

    echo json_encode(['status' => 'ok']);
    exit;
}

// 5️⃣ ACTUALIZAR CANTIDAD DE UN PRODUCTO (pisar con cantidad exacta)
if ($action === 'update' && $id_producto) {
    if ($cantidad < 1) $cantidad = 1;

    $_SESSION['carrito'][$id_producto] = $cantidad;

    if (isset($_SESSION['id_usuario'])) {
        $id_usuario = $_SESSION['id_usuario'];

        $stmt = $conexion->prepare("SELECT id_producto FROM carrito WHERE id_usuario=? AND id_producto=?");
        $stmt->bind_param("ii", $id_usuario, $id_producto);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows > 0) {
            $stmt2 = $conexion->prepare("UPDATE carrito SET cantidad=? WHERE id_usuario=? AND id_producto=?");
            $stmt2->bind_param("iii", $cantidad, $id_usuario, $id_producto);
            $stmt2->execute();
            $stmt2->close();
        } else {
            $stmt2 = $conexion->prepare("INSERT INTO carrito (id_usuario, id_producto, cantidad) VALUES (?,?,?)");
            $stmt2->bind_param("iii", $id_usuario, $id_producto, $cantidad);
            $stmt2->execute();
            $stmt2->close();
        }

        $stmt->close();
    }

    echo json_encode([
        'status' => 'ok',
        'id_producto' => $id_producto,
        'nueva_cantidad' => $cantidad
    ]);
    exit;
}

// 🚨 Acción no válida
echo json_encode(['status' => 'error', 'mensaje' => 'Acción no válida', 'accion_recibida' => $action]);
exit;

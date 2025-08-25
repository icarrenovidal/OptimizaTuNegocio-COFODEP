<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json; charset=utf-8');

include __DIR__ . '/../../Config/conexion.php'; // $conexion como MySQLi

// ------------------
// PARAMETROS
// ------------------
$accion = $_POST['accion'] ?? $_GET['accion'] ?? '';
$id_producto = $_POST['id_producto'] ?? null;
$cantidad = intval($_POST['cantidad'] ?? 1);
if ($cantidad < 1) $cantidad = 1;

// ------------------
// INICIALIZAR CARRITO SESIÓN
// ------------------
if (!isset($_SESSION['carrito'])) $_SESSION['carrito'] = [];

// ------------------
// FUNCIONES AUXILIARES
// ------------------
$baseUrl = '/OptimizaTuNegocio/OptimizaTuNegocio/'; // Base URL absoluta para imágenes

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
            : "https://via.placeholder.com/80";
    }

    return $prod ?: null;
}

// ------------------
// AGREGAR PRODUCTO
// ------------------
if ($accion === 'add' && $id_producto) {
    if (!isset($_SESSION['carrito'][$id_producto])) $_SESSION['carrito'][$id_producto] = 0;
    $_SESSION['carrito'][$id_producto] += $cantidad;

    if (isset($_SESSION['usuario_id'])) {
        $usuario_id = $_SESSION['usuario_id'];

        $stmt = $conexion->prepare("SELECT cantidad FROM carrito WHERE usuario_id=? AND id_producto=?");
        $stmt->bind_param("ii", $usuario_id, $id_producto);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows > 0) {
            $row = $resultado->fetch_assoc();
            $newQty = $row['cantidad'] + $cantidad;
            $stmt2 = $conexion->prepare("UPDATE carrito SET cantidad=? WHERE usuario_id=? AND id_producto=?");
            $stmt2->bind_param("iii", $newQty, $usuario_id, $id_producto);
            $stmt2->execute();
            $stmt2->close();
        } else {
            $stmt2 = $conexion->prepare("INSERT INTO carrito(usuario_id, id_producto, cantidad) VALUES(?,?,?)");
            $stmt2->bind_param("iii", $usuario_id, $id_producto, $cantidad);
            $stmt2->execute();
            $stmt2->close();
        }

        $stmt->close();
    }

    echo json_encode(['status' => 'ok', 'carrito' => $_SESSION['carrito']]);
    exit;
}

// ------------------
// LISTAR CARRITO
// ------------------
if ($accion === 'list') {
    $response = [];

    if (isset($_SESSION['usuario_id'])) {
        $usuario_id = $_SESSION['usuario_id'];
        $sql = "
            SELECT 
                c.id_producto, 
                c.cantidad, 
                p.nombre,
                COALESCE(pp.precio_venta, 0) AS precio,
                ip.ruta AS imagen
            FROM carrito c
            JOIN productos p ON c.id_producto = p.id_producto
            LEFT JOIN precios_productos pp 
                   ON pp.id_producto = p.id_producto 
                  AND (pp.fecha_fin IS NULL OR pp.fecha_fin >= CURDATE())
            LEFT JOIN imagenes_productos ip 
                   ON ip.id_producto = p.id_producto
            WHERE c.usuario_id=?
            ORDER BY ip.fecha_subida DESC, pp.fecha_inicio DESC
        ";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("i", $usuario_id);
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
    } else {
        foreach ($_SESSION['carrito'] as $id => $cant) {
            $prod = obtenerProducto($conexion, $id, $baseUrl);
            if ($prod) {
                $prod['cantidad'] = $cant;
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

    echo json_encode($response);
    exit;
}

// ------------------
// ELIMINAR PRODUCTO
// ------------------
if ($accion === 'remove' && $id_producto) {
    unset($_SESSION['carrito'][$id_producto]);

    if (isset($_SESSION['usuario_id'])) {
        $stmt = $conexion->prepare("DELETE FROM carrito WHERE usuario_id=? AND id_producto=?");
        $stmt->bind_param("ii", $_SESSION['usuario_id'], $id_producto);
        $stmt->execute();
        $stmt->close();
    }

    echo json_encode(['status' => 'ok']);
    exit;
}

// ------------------
// VACÍAR CARRITO
// ------------------
if ($accion === 'clear') {
    $_SESSION['carrito'] = [];

    if (isset($_SESSION['usuario_id'])) {
        $stmt = $conexion->prepare("DELETE FROM carrito WHERE usuario_id=?");
        $stmt->bind_param("i", $_SESSION['usuario_id']);
        $stmt->execute();
        $stmt->close();
    }

    echo json_encode(['status' => 'ok']);
    exit;
}

// ------------------
// ACCIÓN NO VÁLIDA
// ------------------
echo json_encode(['status' => 'error', 'mensaje' => 'Acción no válida', 'accion_recibida' => $accion]);
exit;
?>

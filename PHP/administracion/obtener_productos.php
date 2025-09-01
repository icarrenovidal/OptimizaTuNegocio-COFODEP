<?php
session_start();
include __DIR__ . '/../../Config/conexion.php';

header('Content-Type: application/json; charset=utf-8');

// ================================
// Verificar que el usuario tenga un emprendimiento
// ================================
if (!isset($_SESSION['id_emprendimiento'])) {
    echo json_encode(["productos" => [], "total" => 0]);
    exit;
}
$id_emprendimiento = intval($_SESSION['id_emprendimiento']);

// ================================
// Recibir filtros desde la URL
// ================================
$categoria = $_GET['categoria'] ?? '';
$precio_min = $_GET['precio_min'] ?? '';
$precio_max = $_GET['precio_max'] ?? '';
$nombre = $_GET['nombre'] ?? '';
$stock = $_GET['stock'] ?? '';

// ------------------ PAGINACION ------------------
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
$offset = ($page - 1) * $limit;

// ================================
// Consulta para total de productos filtrados
// ================================
$sql_total = "
    SELECT COUNT(DISTINCT p.id_producto) AS total
    FROM productos p
    LEFT JOIN categorias_productos c ON p.id_categoria = c.id_categoria
    LEFT JOIN lotes l ON l.id_producto = p.id_producto
    LEFT JOIN (
        SELECT id_producto, precio_venta
        FROM precios_productos
        WHERE fecha_fin IS NULL OR fecha_fin >= CURDATE()
        ORDER BY fecha_inicio DESC
    ) pr ON pr.id_producto = p.id_producto
    WHERE p.id_emprendimiento = $id_emprendimiento
";

if($categoria !== '') { $sql_total .= " AND p.id_categoria = " . intval($categoria); }
if($precio_min !== '') { $sql_total .= " AND COALESCE(pr.precio_venta,0) >= " . floatval($precio_min); }
if($precio_max !== '') { $sql_total .= " AND COALESCE(pr.precio_venta,0) <= " . floatval($precio_max); }
if($nombre !== '') { $sql_total .= " AND p.nombre LIKE '%" . $conexion->real_escape_string($nombre) . "%'"; }

$total_result = $conexion->query($sql_total);
$total_productos = ($total_result && $row = $total_result->fetch_assoc()) ? intval($row['total']) : 0;

// ================================
// Consulta principal con LIMIT y OFFSET
// ================================
$sql = "
    SELECT 
        p.id_producto,
        p.nombre AS nombre_producto,
        p.descripcion,
        p.unidad_medida,
        p.estado,
        c.nombre AS nombre_categoria,
        COALESCE(SUM(l.cantidad_actual), 0) AS stock,
        COALESCE(pr.precio_venta, 0) AS precio,
        i.ruta AS imagen
    FROM productos p
    LEFT JOIN categorias_productos c ON p.id_categoria = c.id_categoria
    LEFT JOIN lotes l ON l.id_producto = p.id_producto
    LEFT JOIN (
        SELECT id_producto, precio_venta
        FROM precios_productos
        WHERE fecha_fin IS NULL OR fecha_fin >= CURDATE()
        ORDER BY fecha_inicio DESC
    ) pr ON pr.id_producto = p.id_producto
    LEFT JOIN imagenes_productos i ON i.id_producto = p.id_producto
    WHERE p.id_emprendimiento = $id_emprendimiento
";

if($categoria !== '') { $sql .= " AND p.id_categoria = " . intval($categoria); }
if($precio_min !== '') { $sql .= " AND COALESCE(pr.precio_venta,0) >= " . floatval($precio_min); }
if($precio_max !== '') { $sql .= " AND COALESCE(pr.precio_venta,0) <= " . floatval($precio_max); }
if($nombre !== '') { $sql .= " AND p.nombre LIKE '%" . $conexion->real_escape_string($nombre) . "%'"; }

$sql .= " GROUP BY p.id_producto, i.id_imagen
          ORDER BY p.nombre ASC
          LIMIT $offset, $limit";

// Filtro de stock usando HAVING
if($stock !== '') {
    if($stock === 'disponible') {
        $sql .= " HAVING stock > 5";
    } elseif($stock === 'bajo') {
        $sql .= " HAVING stock BETWEEN 1 AND 5";
    } elseif($stock === 'agotado') {
        $sql .= " HAVING stock = 0";
    }
}

// ================================
// Ejecutar consulta
// ================================
$result = $conexion->query($sql);
$productos = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $id = $row['id_producto'];

        if (!isset($productos[$id])) {
            $productos[$id] = [
                'id_producto' => $id,
                'nombre_producto' => $row['nombre_producto'],
                'descripcion' => $row['descripcion'],
                'unidad_medida' => $row['unidad_medida'],
                'estado' => $row['estado'],
                'nombre_categoria' => $row['nombre_categoria'],
                'stock' => $row['stock'],
                'precio' => $row['precio'],
                'imagenes' => []
            ];
        }

        if ($row['imagen']) {
            $productos[$id]['imagenes'][] = './../../' . $row['imagen'];
        }
    }

    $productos = array_values($productos);
}

// ================================
// Devolver JSON con productos y total
// ================================
echo json_encode([
    "productos" => $productos,
    "total" => $total_productos
], JSON_UNESCAPED_UNICODE);

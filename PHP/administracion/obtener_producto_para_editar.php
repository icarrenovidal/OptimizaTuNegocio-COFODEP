<?php
include __DIR__ . '/../../Config/conexion.php';

if (!isset($_GET['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'ID de producto no proporcionado']);
    exit;
}

$id_producto = intval($_GET['id']);

// Obtener datos del producto
$sql_producto = "SELECT * FROM productos WHERE id_producto = $id_producto LIMIT 1";
$res_producto = $conexion->query($sql_producto);

if ($res_producto->num_rows === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Producto no encontrado']);
    exit;
}

$producto = $res_producto->fetch_assoc();

// Último precio
$sql_precio = "SELECT precio_venta FROM precios_productos WHERE id_producto=$id_producto ORDER BY fecha_inicio DESC LIMIT 1";
$res_precio = $conexion->query($sql_precio);
$precio = ($res_precio->num_rows > 0) ? $res_precio->fetch_assoc()['precio_venta'] : 0;

// Imágenes existentes
$sql_imagenes = "SELECT * FROM imagenes_productos WHERE id_producto=$id_producto";
$res_imagenes = $conexion->query($sql_imagenes);
$imagenes = [];
while ($row = $res_imagenes->fetch_assoc()) {
    $imagenes[] = $row;
}

echo json_encode([
    'status' => 'success',
    'producto' => $producto,
    'precio' => $precio,
    'imagenes' => $imagenes
]);
exit;

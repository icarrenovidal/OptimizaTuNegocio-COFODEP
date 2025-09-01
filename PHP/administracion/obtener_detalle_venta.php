<?php
session_start();
include __DIR__ . '/../../Config/conexion.php';

header('Content-Type: application/json; charset=utf-8');

// Validar sesión y emprendimiento
if (!isset($_SESSION['id_emprendimiento'])) {
    echo json_encode(['venta' => null, 'detalle' => []]);
    exit;
}

$id_emprendimiento = intval($_SESSION['id_emprendimiento']);
$id_venta = intval($_GET['id'] ?? 0);

if ($id_venta <= 0) {
    echo json_encode(['venta' => null, 'detalle' => []]);
    exit;
}

// Obtener información general de la venta
$sql_venta = "
    SELECT 
        v.id_venta,
        v.fecha,
        v.canal_venta,
        v.total
    FROM ventas v
    WHERE v.id_venta = $id_venta
      AND v.id_emprendimiento = $id_emprendimiento
    LIMIT 1
";

$result_venta = $conexion->query($sql_venta);
$venta = null;
if ($result_venta && $result_venta->num_rows > 0) {
    $venta = $result_venta->fetch_assoc();
}

// Obtener detalle de productos de la venta
$sql_detalle = "
    SELECT 
        p.nombre AS producto,
        dv.cantidad,
        dv.precio_unitario,
        (dv.cantidad * dv.precio_unitario) AS subtotal
    FROM detalle_venta dv
    INNER JOIN productos p ON p.id_producto = dv.id_producto
    WHERE dv.id_venta = $id_venta
";

$result_detalle = $conexion->query($sql_detalle);
$detalle = [];
if ($result_detalle) {
    while ($row = $result_detalle->fetch_assoc()) {
        $detalle[] = $row;
    }
}

// Retornar JSON
echo json_encode([
    'venta' => $venta,
    'detalle' => $detalle
], JSON_UNESCAPED_UNICODE);

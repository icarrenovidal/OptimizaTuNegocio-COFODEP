<?php
session_start();
include __DIR__ . '/../../Config/conexion.php';

header('Content-Type: application/json; charset=utf-8');

// Verificar que el usuario tenga un emprendimiento
if (!isset($_SESSION['id_emprendimiento'])) {
    echo json_encode(['ventas'=>[], 'total'=>0]);
    exit;
}
$id_emprendimiento = intval($_SESSION['id_emprendimiento']);

// Filtros opcionales por URL
$fecha_inicio = $_GET['fecha_inicio'] ?? '';
$fecha_fin = $_GET['fecha_fin'] ?? '';
$canal_venta = $_GET['canal_venta'] ?? '';
$metodo_pago = $_GET['metodo_pago'] ?? '';
$page = intval($_GET['page'] ?? 1);
$per_page = 12; // Máximo 12 ventas por página
$offset = ($page - 1) * $per_page;

// Por defecto usar hoy si no se proporcionan fechas
$hoy = date('Y-m-d');
if ($fecha_inicio === '') $fecha_inicio = $hoy;
if ($fecha_fin === '') $fecha_fin = $hoy;

// Ajustar fecha_fin para incluir todo el día
$fecha_fin_completa = $fecha_fin . ' 23:59:59';

// Consulta base
$sql = "
    SELECT 
        v.id_venta,
        v.fecha,
        v.canal_venta,
        v.metodo_pago,
        SUM(dv.cantidad * dv.precio_unitario) AS total
    FROM ventas v
    LEFT JOIN detalle_venta dv ON dv.id_venta = v.id_venta
    WHERE v.id_emprendimiento = $id_emprendimiento
      AND v.fecha >= '" . $conexion->real_escape_string($fecha_inicio) . "'
      AND v.fecha <= '" . $conexion->real_escape_string($fecha_fin_completa) . "'
";

// Aplicar filtros opcionales
if ($canal_venta !== '') {
    $sql .= " AND v.canal_venta = '" . $conexion->real_escape_string($canal_venta) . "'";
}
if ($metodo_pago !== '') {
    $sql .= " AND v.metodo_pago = '" . $conexion->real_escape_string($metodo_pago) . "'";
}

$sql .= "
    GROUP BY v.id_venta, v.fecha, v.canal_venta, v.metodo_pago
    ORDER BY v.fecha DESC
";

// Contar total de ventas para paginación
$count_sql = "SELECT COUNT(*) AS total_ventas FROM ($sql) AS sub";
$total_result = $conexion->query($count_sql);
$total = 0;
if($total_result) {
    $total_row = $total_result->fetch_assoc();
    $total = intval($total_row['total_ventas']);
}

// Agregar límite y offset para paginación
$sql .= " LIMIT $per_page OFFSET $offset";

$result = $conexion->query($sql);

$ventas = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $ventas[] = [
            'id_venta' => $row['id_venta'],
            'fecha' => $row['fecha'],
            'canal_venta' => $row['canal_venta'],
            'metodo_pago' => $row['metodo_pago'],
            'total' => $row['total']
        ];
    }
}

// Retornar JSON con ventas y total
echo json_encode([
    'ventas' => $ventas,
    'total' => $total
], JSON_UNESCAPED_UNICODE);

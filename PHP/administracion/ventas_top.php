<?php
session_start();
require __DIR__ . '/../../Config/auth_check.php';
require __DIR__ . '/../../Config/conexion.php';

header('Content-Type: application/json; charset=UTF-8');

// Verificar sesión
if (empty($_SESSION['id_emprendimiento'])) {
    echo json_encode([]);
    exit;
}

$id_emprendimiento = (int) $_SESSION['id_emprendimiento'];

// Opcionales ?mes=...&anio=...&limite=...
$mes = isset($_GET['mes']) ? max(1, min(12, (int)$_GET['mes'])) : (int)date('n');
$anio = isset($_GET['anio']) ? (int)$_GET['anio'] : (int)date('Y');
$limite = isset($_GET['limite']) ? max(1, min(20, (int)$_GET['limite'])) : 5;

// Rango de fechas del mes
$inicio = sprintf('%04d-%02d-01 00:00:00', $anio, $mes);
$fin = date('Y-m-t 23:59:59', strtotime(sprintf('%04d-%02d-01', $anio, $mes)));

try {
    // OJO: usa 'detalle_venta' (singular) como en tus otros archivos.
    // Si tu tabla es 'detalle_ventas', cámbialo aquí.
    $sql = "
        SELECT 
            p.id_producto,
            p.nombre AS producto,
            SUM(dv.cantidad) AS cantidad
        FROM ventas v
        INNER JOIN detalle_venta dv ON dv.id_venta = v.id_venta
        INNER JOIN productos p ON p.id_producto = dv.id_producto
        WHERE v.id_emprendimiento = ?
          AND v.fecha BETWEEN ? AND ?
        GROUP BY p.id_producto, p.nombre
        ORDER BY cantidad DESC
        LIMIT $limite
    ";

    if (!$stmt = $conexion->prepare($sql)) {
        http_response_code(500);
        echo json_encode(["error" => "Error al preparar la consulta"]);
        exit;
    }

    $stmt->bind_param('iss', $id_emprendimiento, $inicio, $fin);

    if (!$stmt->execute()) {
        http_response_code(500);
        echo json_encode(["error" => "Error al ejecutar la consulta"]);
        exit;
    }

    $result = $stmt->get_result();
    $top = [];
    while ($row = $result->fetch_assoc()) {
        $top[] = [
            "producto" => $row["producto"],
            "cantidad" => (int)$row["cantidad"]
        ];
    }

    echo json_encode($top, JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error al obtener datos: " . $e->getMessage()]);
}

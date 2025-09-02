<?php
session_start();
include __DIR__ . '/../../Config/conexion.php';

// ================================
// Desactivar errores para no romper JSON
// ================================
ini_set('display_errors', 0);
error_reporting(0);

// ================================
// Verificar que el usuario tenga un emprendimiento
// ================================
if (!isset($_SESSION['id_emprendimiento'])) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        "totales" => [
            "hoy" => 0,
            "ayer" => 0,
            "este_mes" => 0,
            "mes_anterior" => 0
        ],
        "grafico" => []
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$id_emprendimiento = intval($_SESSION['id_emprendimiento']);

// ================================
// Parámetro: tipo de agrupación (dia o mes)
// ================================
$tipo = $_GET['tipo'] ?? 'dia'; // 'dia' o 'mes'

// ================================
// Fechas para totales rápidos
// ================================
$hoy = date('Y-m-d');
$ayer = date('Y-m-d', strtotime('-1 day'));
$primerDiaMes = date('Y-m-01');
$primerDiaMesAnterior = date('Y-m-01', strtotime('-1 month'));
$ultimoDiaMesAnterior = date('Y-m-t', strtotime('-1 month'));

// ================================
// Totales rápidos
// ================================
$sql_totales = "
    SELECT
        SUM(CASE WHEN DATE(fecha) = '$hoy' THEN total ELSE 0 END) AS hoy,
        SUM(CASE WHEN DATE(fecha) = '$ayer' THEN total ELSE 0 END) AS ayer,
        SUM(CASE WHEN DATE(fecha) >= '$primerDiaMes' THEN total ELSE 0 END) AS este_mes,
        SUM(CASE WHEN DATE(fecha) BETWEEN '$primerDiaMesAnterior' AND '$ultimoDiaMesAnterior' THEN total ELSE 0 END) AS mes_anterior
    FROM ventas
    WHERE id_emprendimiento = $id_emprendimiento
";

$result_totales = $conexion->query($sql_totales);
$totales = ($result_totales && $row = $result_totales->fetch_assoc()) ? $row : [
    "hoy" => 0,
    "ayer" => 0,
    "este_mes" => 0,
    "mes_anterior" => 0
];

// ================================
// Datos para gráfico dinámico
// ================================
$grafico = [];

if ($tipo === 'dia') {
    // Últimos 7 días
    $sql_grafico = "
        SELECT DATE(fecha) as dia, SUM(total) as total
        FROM ventas
        WHERE id_emprendimiento = $id_emprendimiento
        AND fecha >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
        GROUP BY DATE(fecha)
        ORDER BY DATE(fecha)
    ";
    $result = $conexion->query($sql_grafico);
    while ($row = $result->fetch_assoc()) {
        $grafico[] = [
            "label" => $row['dia'],
            "total" => floatval($row['total'])
        ];
    }
} else {
    // Últimos 6 meses
    $sql_grafico = "
        SELECT DATE_FORMAT(fecha, '%Y-%m') as mes, SUM(total) as total
        FROM ventas
        WHERE id_emprendimiento = $id_emprendimiento
        AND fecha >= DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 5 MONTH), '%Y-%m-01')
        GROUP BY DATE_FORMAT(fecha, '%Y-%m')
        ORDER BY DATE_FORMAT(fecha, '%Y-%m')
    ";
    $result = $conexion->query($sql_grafico);
    while ($row = $result->fetch_assoc()) {
        $grafico[] = [
            "label" => $row['mes'],
            "total" => floatval($row['total'])
        ];
    }
}

// ================================
// Devolver JSON limpio
// ================================
ob_clean();
header('Content-Type: application/json; charset=utf-8');
echo json_encode([
    "totales" => $totales,
    "grafico" => $grafico
], JSON_UNESCAPED_UNICODE);
exit;

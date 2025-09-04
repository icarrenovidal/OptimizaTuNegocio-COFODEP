<?php
include __DIR__ . "/../../Config/conexion.php";

header("Content-Type: application/json; charset=UTF-8");

try {
    $sql = "
        SELECT 
            DATE_FORMAT(fecha_registro, '%Y-%m') AS mes,
            COUNT(*) AS cantidad
        FROM usuarios
        GROUP BY mes
        ORDER BY mes ASC
    ";

    $result = $conexion->query($sql);
    $datos = [];

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $datos[] = $row;
        }
    }

    echo json_encode($datos, JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}

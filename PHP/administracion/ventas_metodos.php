<?php
include __DIR__ . "/../../Config/conexion.php";
include __DIR__ . "/../../Config/auth_check.php";

header("Content-Type: application/json; charset=UTF-8");

try {
    $sql = "
        SELECT 
            v.metodo_pago AS metodo,
            COUNT(*) AS cantidad
        FROM ventas v
        WHERE MONTH(v.fecha) = MONTH(CURRENT_DATE())
          AND YEAR(v.fecha) = YEAR(CURRENT_DATE())
        GROUP BY v.metodo_pago
        ORDER BY cantidad DESC
    ";

    $result = $conexion->query($sql);

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    echo json_encode($data, JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error al obtener datos: " . $e->getMessage()]);
}

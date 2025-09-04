<?php
include __DIR__ . "/../../Config/conexion.php";

header("Content-Type: application/json; charset=UTF-8");

try {
    $sql = "
        SELECT COUNT(*) AS total
        FROM emprendimientos e
        INNER JOIN usuarios u ON e.id_usuario = u.id_usuario
        WHERE u.estado = 'activo'
    ";

    $result = $conexion->query($sql);

    $data = ["total" => 0];
    if ($result && $row = $result->fetch_assoc()) {
        $data["total"] = (int)$row["total"];
    }

    echo json_encode($data, JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}

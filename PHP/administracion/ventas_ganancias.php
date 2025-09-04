<?php
include __DIR__ . "/../../Config/conexion.php";
include __DIR__ . "/../../Config/auth_check.php";

header("Content-Type: application/json; charset=UTF-8");

try {
    $sql = "
        SELECT 
            p.nombre AS producto,
            SUM(dv.subtotal) AS ingresos,
            SUM(pr.costo_unitario * dv.cantidad) AS costos,
            (SUM(dv.subtotal) - SUM(pr.costo_unitario * dv.cantidad)) AS ganancia
        FROM ventas v
        INNER JOIN detalle_venta dv ON v.id_venta = dv.id_venta
        INNER JOIN productos p ON dv.id_producto = p.id_producto
        INNER JOIN precios_productos pr ON pr.id_producto = p.id_producto
            AND v.fecha BETWEEN pr.fecha_inicio AND IFNULL(pr.fecha_fin, NOW())
        WHERE MONTH(v.fecha) = MONTH(CURRENT_DATE())
          AND YEAR(v.fecha) = YEAR(CURRENT_DATE())
        GROUP BY p.id_producto
        ORDER BY ganancia DESC
        LIMIT 5
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

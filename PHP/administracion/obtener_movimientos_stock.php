<?php
header('Content-Type: application/json');
include __DIR__ . '/../../Config/conexion.php';

try {
    // Obtener el id_emprendimiento del usuario
    session_start();
    $id_emprendimiento = $_SESSION['id_emprendimiento'] ?? null;

    if (!$id_emprendimiento) {
        throw new Exception("Emprendimiento no identificado.");
    }

    $sql = "SELECT m.id_movimiento, p.nombre AS nombre_producto, l.codigo_lote, 
                   m.tipo, m.cantidad, m.fecha, m.origen, m.observacion
            FROM movimientos_stock m
            LEFT JOIN productos p ON m.id_producto = p.id_producto
            LEFT JOIN lotes l ON m.id_lote = l.id_lote
            WHERE p.id_emprendimiento = ?
            ORDER BY m.fecha DESC";

    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id_emprendimiento);
    $stmt->execute();
    $result = $stmt->get_result();

    $movimientos = [];
    while ($row = $result->fetch_assoc()) {
        $movimientos[] = $row;
    }

    echo json_encode($movimientos);

} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

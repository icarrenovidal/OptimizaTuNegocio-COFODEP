<?php
// Evitar cualquier salida antes del JSON
ob_start();

header('Content-Type: application/json');
include __DIR__ . '/../../Config/conexion.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Método no permitido");
    }

    $id_producto = $_POST['id_producto'] ?? null;
    $id_lote = $_POST['id_lote'] ?? null;
    $cantidad = $_POST['cantidad'] ?? null;
    $origen = $_POST['origen'] ?? 'ajuste_inventario';
    $observacion = $_POST['observacion'] ?? '';

    if (!$id_producto || !$cantidad || $cantidad <= 0) {
        throw new Exception("Datos incompletos o cantidad inválida.");
    }

    $cantidad = (int)$cantidad;

    // ======================
    // Verificar stock del producto o lote
    // ======================
    if ($id_lote) {
        // Stock de lote específico
        $stmt = $conexion->prepare("SELECT cantidad_actual FROM lotes WHERE id_lote = ? AND id_producto = ?");
        $stmt->bind_param("ii", $id_lote, $id_producto);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $lote = $resultado->fetch_assoc();

        if (!$lote) throw new Exception("Lote no encontrado.");
        if ($lote['cantidad_actual'] < $cantidad) throw new Exception("Cantidad mayor al stock disponible en el lote.");
    } else {
        // Stock total del producto
        $stmt = $conexion->prepare("SELECT COALESCE(SUM(cantidad_actual),0) AS stock_total FROM lotes WHERE id_producto = ?");
        $stmt->bind_param("i", $id_producto);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $productoStock = $resultado->fetch_assoc();

        if ($productoStock['stock_total'] < $cantidad) throw new Exception("Cantidad mayor al stock total del producto.");
    }

    // ======================
    // Insertar movimiento
    // ======================
    $stmt = $conexion->prepare("
        INSERT INTO movimientos_stock 
        (id_lote, id_producto, tipo, cantidad, fecha, observacion, origen)
        VALUES (?, ?, 'salida', ?, NOW(), ?, ?)
    ");
    $stmt->bind_param("iiiss", $id_lote_param, $id_producto, $cantidad, $observacion, $origen);
    $id_lote_param = $id_lote ?: null;
    $stmt->execute();

    // ======================
    // Actualizar stock
    // ======================
    if ($id_lote) {
        $stmt = $conexion->prepare("UPDATE lotes SET cantidad_actual = cantidad_actual - ? WHERE id_lote = ?");
        $stmt->bind_param("ii", $cantidad, $id_lote);
        $stmt->execute();
    } else {
        // Descontar de lotes disponibles de manera FIFO
        $stmt = $conexion->prepare("SELECT id_lote, cantidad_actual FROM lotes WHERE id_producto = ? AND cantidad_actual > 0 ORDER BY fecha_ingreso ASC");
        $stmt->bind_param("i", $id_producto);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $lotes = $resultado->fetch_all(MYSQLI_ASSOC);

        $restante = $cantidad;
        foreach ($lotes as $lote) {
            if ($restante <= 0) break;
            $descontar = min($restante, $lote['cantidad_actual']);

            $stmtUpdate = $conexion->prepare("UPDATE lotes SET cantidad_actual = cantidad_actual - ? WHERE id_lote = ?");
            $stmtUpdate->bind_param("ii", $descontar, $lote['id_lote']);
            $stmtUpdate->execute();

            $restante -= $descontar;
        }
    }

    // Limpiar cualquier salida accidental antes de enviar JSON
    ob_end_clean();
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    ob_end_clean();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

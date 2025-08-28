<?php
// /Pages/administracion/procesar_venta.php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json; charset=utf-8');

include __DIR__ . '/../../Config/conexion.php'; // $conexion es MySQLi

try {
    // ---- Validaciones básicas ----
    if (empty($_SESSION['id_usuario'])) {
        throw new Exception("Usuario no logueado. Inicia sesión para completar la compra.");
    }
    $id_usuario = intval($_SESSION['id_usuario']);

    // id_emprendimiento: prefer POST, si no existe en session usamos Session o 1 por defecto
    $id_emprendimiento = isset($_POST['id_emprendimiento']) ? intval($_POST['id_emprendimiento']) :
                         (isset($_SESSION['id_emprendimiento']) ? intval($_SESSION['id_emprendimiento']) : 1);

    // canal_venta (validar enum)
    $canal_venta = isset($_POST['canal_venta']) ? trim($_POST['canal_venta']) : 'otro';
    $canales_validos = ['ferias','redes sociales','tienda física','otro'];
    if (!in_array($canal_venta, $canales_validos)) $canal_venta = 'otro';

    // metodo pago (desde el select del frontend)
    $metodo_pago = isset($_POST['payment_method']) ? trim($_POST['payment_method']) : null;

    // ---- Iniciar transacción ----
    if (!$conexion->begin_transaction()) {
        throw new Exception("No se pudo iniciar transacción: " . $conexion->error);
    }

    // ---- Obtener carrito desde DB (usuario logueado) ----
    $stmt = $conexion->prepare("
        SELECT c.id_carrito, c.id_producto, c.cantidad
        FROM carrito c
        WHERE c.id_usuario = ?
    ");
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $res = $stmt->get_result();
    $carrito = $res->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    if (empty($carrito)) {
        throw new Exception("Carrito vacío.");
    }

    // ---- Calcular total usando precios desde la BD ----
    $total = 0;
    $items = [];
    $stmtPrecio = $conexion->prepare("
        SELECT COALESCE(pp.precio_venta, 0) AS precio
        FROM precios_productos pp
        WHERE pp.id_producto = ?
          AND (pp.fecha_fin IS NULL OR pp.fecha_fin >= CURDATE())
        ORDER BY pp.fecha_inicio DESC
        LIMIT 1
    ");

    foreach ($carrito as $row) {
        $id_prod = intval($row['id_producto']);
        $cant = intval($row['cantidad']);

        $stmtPrecio->bind_param("i", $id_prod);
        $stmtPrecio->execute();
        $r = $stmtPrecio->get_result()->fetch_assoc();
        $precio = isset($r['precio']) ? floatval($r['precio']) : 0;

        $subtotal = $precio * $cant;
        $total += $subtotal;

        $items[] = [
            'id_producto' => $id_prod,
            'cantidad' => $cant,
            'precio_unitario' => $precio
        ];
    }
    $stmtPrecio->close();

    // ---- Insertar en ventas ----
    $sqlVenta = "INSERT INTO ventas (id_emprendimiento, canal_venta, total, metodo_pago) VALUES (?, ?, ?, ?)";
    $stmtVenta = $conexion->prepare($sqlVenta);
    if (!$stmtVenta) throw new Exception("Error preparando INSERT ventas: " . $conexion->error);
    $stmtVenta->bind_param("isis", $id_emprendimiento, $canal_venta, $total, $metodo_pago);
    if (!$stmtVenta->execute()) throw new Exception("Error insertando ventas: " . $stmtVenta->error);
    $id_venta = $conexion->insert_id;
    $stmtVenta->close();

    // ---- Preparar statements reutilizables ----
    $stmtLotes = $conexion->prepare("
        SELECT id_lote, cantidad_actual
        FROM lotes
        WHERE id_producto = ? AND cantidad_actual > 0
        ORDER BY fecha_ingreso ASC
        FOR UPDATE
    ");
    $stmtUpdateLote = $conexion->prepare("UPDATE lotes SET cantidad_actual = ? WHERE id_lote = ?");
    $stmtInsertDetalle = $conexion->prepare("
        INSERT INTO detalle_venta (id_venta, id_producto, id_lote, cantidad, precio_unitario, subtotal)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmtInsertMovimiento = $conexion->prepare("
        INSERT INTO movimientos_stock (id_lote, id_producto, tipo, cantidad, fecha, observacion, origen)
        VALUES (?, ?, 'salida', ?, NOW(), ?, 'venta')
    ");

    if (!$stmtLotes || !$stmtUpdateLote || !$stmtInsertDetalle || !$stmtInsertMovimiento) {
        throw new Exception("Error preparando statements: " . $conexion->error);
    }

    // ---- Aplicar FIFO sobre lotes ----
    foreach ($items as $it) {
        $id_producto = $it['id_producto'];
        $cantidad_pendiente = $it['cantidad'];
        $precio_unitario = $it['precio_unitario'];

        $stmtLotes->bind_param("i", $id_producto);
        if (!$stmtLotes->execute()) throw new Exception("Error ejecutando SELECT lotes: " . $stmtLotes->error);
        $resLotes = $stmtLotes->get_result();

        while ($cantidad_pendiente > 0 && $lote = $resLotes->fetch_assoc()) {
            $id_lote = intval($lote['id_lote']);
            $disponible = intval($lote['cantidad_actual']);

            if ($disponible >= $cantidad_pendiente) {
                $nuevo = $disponible - $cantidad_pendiente;
                $stmtUpdateLote->bind_param("ii", $nuevo, $id_lote);
                if (!$stmtUpdateLote->execute()) throw new Exception("Error actualizando lote: " . $stmtUpdateLote->error);

                $subtotal = $precio_unitario * $cantidad_pendiente;
                $stmtInsertDetalle->bind_param("iiiiid", $id_venta, $id_producto, $id_lote, $cantidad_pendiente, $precio_unitario, $subtotal);
                if (!$stmtInsertDetalle->execute()) throw new Exception("Error insertando detalle_venta: " . $stmtInsertDetalle->error);

                $observ = "Venta #$id_venta";
                $stmtInsertMovimiento->bind_param("iiis", $id_lote, $id_producto, $cantidad_pendiente, $observ);
                if (!$stmtInsertMovimiento->execute()) throw new Exception("Error insertando movimiento_stock: " . $stmtInsertMovimiento->error);

                $cantidad_pendiente = 0;
            } else {
                $stmtUpdateLote->bind_param("ii", $zero = 0, $id_lote);
                if (!$stmtUpdateLote->execute()) throw new Exception("Error actualizando lote a 0: " . $stmtUpdateLote->error);

                $subtotal = $precio_unitario * $disponible;
                $stmtInsertDetalle->bind_param("iiiiid", $id_venta, $id_producto, $id_lote, $disponible, $precio_unitario, $subtotal);
                if (!$stmtInsertDetalle->execute()) throw new Exception("Error insertando detalle_venta (multi-lote): " . $stmtInsertDetalle->error);

                $observ = "Venta #$id_venta (consumido lote)";
                $stmtInsertMovimiento->bind_param("iiis", $id_lote, $id_producto, $disponible, $observ);
                if (!$stmtInsertMovimiento->execute()) throw new Exception("Error insertando movimiento_stock (multi-lote): " . $stmtInsertMovimiento->error);

                $cantidad_pendiente -= $disponible;
            }
        }

        if ($cantidad_pendiente > 0) {
            throw new Exception("Stock insuficiente para el producto ID {$id_producto}. Falta: {$cantidad_pendiente}");
        }
    }

    // ---- Vaciar carrito ----
    $stmtDel = $conexion->prepare("DELETE FROM carrito WHERE id_usuario = ?");
    $stmtDel->bind_param("i", $id_usuario);
    if (!$stmtDel->execute()) throw new Exception("Error vaciando carrito: " . $stmtDel->error);
    $stmtDel->close();

    // ---- Auditoría opcional ----
    if ($stmtAud = $conexion->prepare("INSERT INTO auditoria (id_usuario, accion, fecha, detalles) VALUES (?, 'REGISTRO VENTA', NOW(), ?)")) {
        $detalles = "Venta #$id_venta por usuario $id_usuario";
        $stmtAud->bind_param("is", $id_usuario, $detalles);
        $stmtAud->execute();
        $stmtAud->close();
    }

    // ---- Commit ----
    $conexion->commit();

    echo json_encode([
        'status' => 'ok',
        'id_venta' => $id_venta,
        'total' => $total,
        'message' => 'Venta registrada correctamente.'
    ]);
    exit;

} catch (Exception $e) {
    if (isset($conexion) && $conexion->connect_errno == 0) {
        $conexion->rollback();
    }
    http_response_code(400);
    echo json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
    exit;
}
?>

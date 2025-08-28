<?php
session_start();
include __DIR__ . '/../../Config/conexion.php';

// Verificar que venga id_producto y cantidad
if (!isset($_POST['id_producto'], $_POST['cantidad'])) {
    die("Error: datos incompletos.");
}

$id_producto = (int)$_POST['id_producto'];
$cantidad = (int)$_POST['cantidad'];
$codigo_lote = $_POST['codigo_lote'] ?? '';
$fecha_ingreso = $_POST['fecha_ingreso'] ?? date('Y-m-d');
$fecha_vencimiento = $_POST['fecha_vencimiento'] ?? null;
$observacion = $_POST['observacion'] ?? 'Ingreso de stock';

// Validar cantidad
if ($cantidad <= 0) {
    die("La cantidad debe ser mayor a cero.");
}

// Verificar que el producto exista
$stmt = $conexion->prepare("SELECT id_producto, nombre FROM productos WHERE id_producto = ?");
$stmt->bind_param("i", $id_producto);
$stmt->execute();
$result = $stmt->get_result();
$producto = $result->fetch_assoc();
$stmt->close();

if (!$producto) {
    die("Producto no encontrado.");
}

// Preparar SQL para insertar lote
$sql_lote = "INSERT INTO lotes (id_producto, codigo_lote, cantidad_inicial, cantidad_actual, fecha_ingreso, fecha_vencimiento)
             VALUES (?, ?, ?, ?, ?, " . ($fecha_vencimiento ? "?" : "NULL") . ")";
$stmt = $conexion->prepare($sql_lote);

// Bind de parámetros según si hay fecha de vencimiento
if ($fecha_vencimiento) {
    $stmt->bind_param("isisss", $id_producto, $codigo_lote, $cantidad, $cantidad, $fecha_ingreso, $fecha_vencimiento);
} else {
    $stmt->bind_param("isiss", $id_producto, $codigo_lote, $cantidad, $cantidad, $fecha_ingreso);
}

// Ejecutar insert de lote
if (!$stmt->execute()) {
    die("Error al insertar lote: " . $stmt->error);
}

// Obtener id_lote recién insertado
$id_lote = $stmt->insert_id;
$stmt->close();

// Insertar movimiento de stock
$tipo = 'entrada';
$origen = 'compra';

$stmt = $conexion->prepare("
    INSERT INTO movimientos_stock (id_lote, id_producto, tipo, cantidad, fecha, observacion, origen)
    VALUES (?, ?, ?, ?, NOW(), ?, ?)
");
$stmt->bind_param("iissss", $id_lote, $id_producto, $tipo, $cantidad, $observacion, $origen);

if (!$stmt->execute()) {
    die("Error al registrar movimiento de stock: " . $stmt->error);
}

$stmt->close();

// Redirigir con mensaje de éxito
header("Location: ./../../Pages/administracion/ver_productos.php?mensaje=Stock agregado exitosamente");
exit;

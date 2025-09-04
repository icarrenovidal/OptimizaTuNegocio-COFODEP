<?php
include __DIR__ . '/../../Config/conexion.php';

header('Content-Type: application/json; charset=utf-8');

$id = intval($_GET['id'] ?? 0);

if ($id <= 0) {
    echo json_encode(["error" => "Producto no válido"]);
    exit;
}

// ================================
// Traer datos básicos del producto
// ================================
$sql = "
SELECT 
    p.id_producto, 
    p.nombre AS nombre_producto, 
    p.descripcion, 
    p.unidad_medida,
    c.nombre AS nombre_categoria,
    COALESCE(SUM(l.cantidad_actual), 0) AS stock,
    COALESCE(
        (
            SELECT pp.precio_venta
            FROM precios_productos pp
            WHERE pp.id_producto = p.id_producto
              AND (pp.fecha_fin IS NULL OR pp.fecha_fin >= CURDATE())
            ORDER BY pp.fecha_inicio DESC
            LIMIT 1
        ),
        0
    ) AS precio
FROM productos p
LEFT JOIN categorias_productos c ON p.id_categoria = c.id_categoria
LEFT JOIN lotes l ON l.id_producto = p.id_producto
WHERE p.id_producto = ?
GROUP BY p.id_producto, p.nombre, p.descripcion, p.unidad_medida, c.nombre
";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$producto = $result->fetch_assoc();

if (!$producto) {
    echo json_encode(["error" => "Producto no encontrado"]);
    exit;
}

// ================================
// Traer imágenes del producto
// ================================
$imagenes = [];
$sqlImgs = "SELECT ruta FROM imagenes_productos WHERE id_producto = ? ORDER BY id_imagen ASC";
$stmtImg = $conexion->prepare($sqlImgs);
$stmtImg->bind_param("i", $id);
$stmtImg->execute();
$resImgs = $stmtImg->get_result();

while ($img = $resImgs->fetch_assoc()) {
    $imagenes[] = './../../' . $img['ruta'];
}
$producto['imagenes'] = $imagenes;

// ================================
// Traer lotes del producto
// ================================
$sql_lotes = "
    SELECT id_lote, id_producto, codigo_lote, cantidad_inicial, cantidad_actual, fecha_ingreso, fecha_vencimiento
    FROM lotes
    WHERE id_producto = ?
    ORDER BY fecha_ingreso DESC
";
$stmt_lotes = $conexion->prepare($sql_lotes);
$stmt_lotes->bind_param("i", $id);
$stmt_lotes->execute();
$result_lotes = $stmt_lotes->get_result();
$lotes = $result_lotes->fetch_all(MYSQLI_ASSOC);
$stmt_lotes->close();

// ================================
// Devolver JSON
// ================================
echo json_encode([
    "producto" => $producto,
    "lotes" => $lotes
], JSON_UNESCAPED_UNICODE);

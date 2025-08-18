<?php
include __DIR__ . '/../../Config/conexion.php';

$id = intval($_GET['id'] ?? 0);

if ($id <= 0) {
    die("Producto no válido.");
}

// Traer datos básicos del producto (sin imágenes todavía)
$sql = "
    SELECT p.id_producto, p.nombre AS nombre_producto, p.descripcion, p.unidad_medida,
           c.nombre AS nombre_categoria,
           COALESCE(l.cantidad_actual, 0) AS stock,
           COALESCE(pr.precio_venta, 0) AS precio
    FROM productos p
    LEFT JOIN categorias_productos c ON p.id_categoria = c.id_categoria
    LEFT JOIN lotes l ON l.id_producto = p.id_producto
    LEFT JOIN (
        SELECT pp1.id_producto, pp1.precio_venta
        FROM precios_productos pp1
        INNER JOIN (
            SELECT id_producto, MAX(fecha_inicio) AS max_fecha
            FROM precios_productos
            WHERE fecha_fin IS NULL OR fecha_fin >= CURDATE()
            GROUP BY id_producto
        ) pp2 
        ON pp1.id_producto = pp2.id_producto AND pp1.fecha_inicio = pp2.max_fecha
    ) pr ON pr.id_producto = p.id_producto
    WHERE p.id_producto = ?
";


$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$producto = $result->fetch_assoc();

// Si existe el producto, traemos las imágenes
if ($producto) {
    $imagenes = [];
    $sqlImgs = "SELECT ruta FROM imagenes_productos WHERE id_producto = ? ORDER BY id_imagen ASC";
    $stmtImg = $conexion->prepare($sqlImgs);
    $stmtImg->bind_param("i", $id);
    $stmtImg->execute();
    $resImgs = $stmtImg->get_result();
    
    while ($img = $resImgs->fetch_assoc()) {
        // mismo estilo que en obtener_productos.php
        $imagenes[] = './../../' . $img['ruta'];
    }
    
    $producto['imagenes'] = $imagenes;
}


// Obtener lotes del producto actual
$id_producto = $producto['id_producto'];
$sql_lotes = "
    SELECT id_lote, id_producto, codigo_lote, cantidad_inicial, cantidad_actual, fecha_ingreso, fecha_vencimiento
    FROM lotes
    WHERE id_producto = ?
    ORDER BY fecha_ingreso DESC
";
$stmt_lotes = $conexion->prepare($sql_lotes);
$stmt_lotes->bind_param("i", $id_producto);
$stmt_lotes->execute();
$result_lotes = $stmt_lotes->get_result();
$lotes = $result_lotes->fetch_all(MYSQLI_ASSOC);
$stmt_lotes->close();
?>
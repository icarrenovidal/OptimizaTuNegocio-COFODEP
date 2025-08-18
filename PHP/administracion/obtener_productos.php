<?php
include __DIR__ . '/../../Config/conexion.php';

// Obtener productos sin limitar a 1 imagen
$sql = "
    SELECT p.id_producto, p.nombre AS nombre_producto, p.descripcion, p.unidad_medida,
           c.nombre AS nombre_categoria,
           COALESCE(l.cantidad_actual, 0) AS stock,
           COALESCE(pr.precio_venta, 0) AS precio
    FROM productos p
    LEFT JOIN categorias_productos c ON p.id_categoria = c.id_categoria
    LEFT JOIN lotes l ON l.id_producto = p.id_producto
    LEFT JOIN (
        SELECT id_producto, precio_venta
        FROM precios_productos
        WHERE fecha_fin IS NULL OR fecha_fin >= CURDATE()
        ORDER BY fecha_inicio DESC
    ) pr ON pr.id_producto = p.id_producto
    ORDER BY p.nombre ASC
";

$result = $conexion->query($sql);
$productos = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $id_producto = $row['id_producto'];
        // Obtener todas las imágenes del producto
        $imagenes = [];
        $res_imgs = $conexion->query("SELECT ruta FROM imagenes_productos WHERE id_producto = $id_producto ORDER BY id_imagen ASC");
        if ($res_imgs) {
            while ($img = $res_imgs->fetch_assoc()) {
                $imagenes[] = './../../' . $img['ruta'];
            }
        }
        $row['imagenes'] = $imagenes; // agregamos el array
        $productos[] = $row;
    }
}
?>

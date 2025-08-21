<?php
include __DIR__ . '/../../Config/conexion.php';

$producto = null;

if (isset($_GET['id'])) {
    $id_producto = $_GET['id'];

    $stmt = $conexion->prepare("
        SELECT p.id_producto, p.nombre, p.descripcion, p.unidad_medida,
               c.nombre AS nombre_categoria,
               COALESCE(SUM(l.cantidad_actual), 0) AS stock
        FROM productos p
        LEFT JOIN categorias_productos c ON p.id_categoria = c.id_categoria
        LEFT JOIN lotes l ON l.id_producto = p.id_producto
        WHERE p.id_producto = ?
        GROUP BY p.id_producto, p.nombre, p.descripcion, p.unidad_medida, c.nombre
    ");
    $stmt->bind_param("i", $id_producto);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado && $resultado->num_rows > 0) {
        $producto = $resultado->fetch_assoc();

        // Traer todas las imágenes del producto
        $imagenes = [];
        $res_imgs = $conexion->query("SELECT ruta FROM imagenes_productos WHERE id_producto = $id_producto ORDER BY id_imagen ASC");
        if ($res_imgs) {
            while ($img = $res_imgs->fetch_assoc()) {
                $imagenes[] = './../../' . $img['ruta'];
            }
        }
        $producto['imagenes'] = $imagenes;
    }

    $stmt->close();
}
?>

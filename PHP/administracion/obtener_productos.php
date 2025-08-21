<?php
include __DIR__ . '/../../Config/conexion.php';

header('Content-Type: application/json; charset=utf-8');

// Recibir filtros desde la URL
$categoria = $_GET['categoria'] ?? '';
$precio_min = $_GET['precio_min'] ?? '';
$precio_max = $_GET['precio_max'] ?? '';
$nombre = $_GET['nombre'] ?? '';
$stock = $_GET['stock'] ?? '';

// Consulta base
$sql = "
    SELECT 
        p.id_producto,
        p.nombre AS nombre_producto,
        p.descripcion,
        p.unidad_medida,
        c.nombre AS nombre_categoria,
        COALESCE(SUM(l.cantidad_actual), 0) AS stock,
        COALESCE(pr.precio_venta, 0) AS precio,
        i.ruta AS imagen
    FROM productos p
    LEFT JOIN categorias_productos c ON p.id_categoria = c.id_categoria
    LEFT JOIN lotes l ON l.id_producto = p.id_producto
    LEFT JOIN (
        SELECT id_producto, precio_venta
        FROM precios_productos
        WHERE fecha_fin IS NULL OR fecha_fin >= CURDATE()
        ORDER BY fecha_inicio DESC
    ) pr ON pr.id_producto = p.id_producto
    LEFT JOIN imagenes_productos i ON i.id_producto = p.id_producto
    WHERE 1
";

// Filtros simples
if($categoria !== '') {
    $sql .= " AND p.id_categoria = " . intval($categoria);
}
if($precio_min !== '') {
    $sql .= " AND COALESCE(pr.precio_venta, 0) >= " . floatval($precio_min);
}
if($precio_max !== '') {
    $sql .= " AND COALESCE(pr.precio_venta, 0) <= " . floatval($precio_max);
}
if($nombre !== '') {
    $sql .= " AND p.nombre LIKE '%" . $conexion->real_escape_string($nombre) . "%'";
}

// Agrupar y ordenar
$sql .= " GROUP BY p.id_producto, i.id_imagen
          ORDER BY p.nombre ASC";

// Filtro de stock usando HAVING (porque stock es SUM)
if($stock !== '') {
    if($stock === 'disponible') {
        $sql .= " HAVING stock > 5"; // Ajusta el límite de "bajo" según tu necesidad
    } elseif($stock === 'bajo') {
        $sql .= " HAVING stock BETWEEN 1 AND 5";
    } elseif($stock === 'agotado') {
        $sql .= " HAVING stock = 0";
    }
}

// Ejecutar consulta
$result = $conexion->query($sql);
$productos = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $id = $row['id_producto'];

        // Inicializar producto si no existe
        if (!isset($productos[$id])) {
            $productos[$id] = [
                'id_producto' => $id,
                'nombre_producto' => $row['nombre_producto'],
                'descripcion' => $row['descripcion'],
                'unidad_medida' => $row['unidad_medida'],
                'nombre_categoria' => $row['nombre_categoria'],
                'stock' => $row['stock'],
                'precio' => $row['precio'],
                'imagenes' => []
            ];
        }

        // Agregar imagen
        if ($row['imagen']) {
            $productos[$id]['imagenes'][] = './../../' . $row['imagen'];
        }
    }

    // Reindexar array
    $productos = array_values($productos);
}

echo json_encode($productos, JSON_UNESCAPED_UNICODE);

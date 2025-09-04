<?php
include __DIR__ . '/../../Config/conexion.php';

header('Content-Type: application/json');

// Verificar que llegue el ID del producto
if (!isset($_POST['id_producto'])) {
    echo json_encode(['status' => 'error', 'message' => 'ID de producto no proporcionado']);
    exit;
}

$id_producto = intval($_POST['id_producto']);
$nombre = trim($_POST['nombre']);
$descripcion = trim($_POST['descripcion']);
$precio_venta = floatval($_POST['precio_venta']);
$estado = $_POST['estado'];

// Validar datos básicos
if ($nombre === '' || $precio_venta <= 0 || !in_array($estado, ['activo', 'inactivo'])) {
    echo json_encode(['status' => 'error', 'message' => 'Datos inválidos']);
    exit;
}

// 1️⃣ Actualizar datos del producto
$stmt = $conexion->prepare("UPDATE productos SET nombre = ?, descripcion = ?, estado = ? WHERE id_producto = ?");
$stmt->bind_param("sssi", $nombre, $descripcion, $estado, $id_producto);
$stmt->execute();

// 2️⃣ Actualizar precio (insertar un nuevo registro en precios_productos)
$stmtPrecio = $conexion->prepare("INSERT INTO precios_productos (id_producto, precio_venta, fecha_inicio) VALUES (?, ?, NOW())");
$stmtPrecio->bind_param("id", $id_producto, $precio_venta);
$stmtPrecio->execute();

// 3️⃣ Eliminar imágenes marcadas
if (isset($_POST['eliminar_imagen'])) {
    foreach ($_POST['eliminar_imagen'] as $id_img) {
        $id_img = intval($id_img);
        // Obtener ruta de la imagen antes de borrar
        $res = $conexion->query("SELECT ruta FROM imagenes_productos WHERE id_imagen = $id_img LIMIT 1");
        if ($res && $res->num_rows > 0) {
            $row = $res->fetch_assoc();
            $ruta = __DIR__ . '/../../' . $row['ruta'];
            if (file_exists($ruta)) unlink($ruta);
        }
        // Borrar registro de la base
        $conexion->query("DELETE FROM imagenes_productos WHERE id_imagen = $id_img");
    }
}

// 4️⃣ Subir nuevas imágenes
if (isset($_FILES['nuevas_imagenes'])) {
    $imagenes = $_FILES['nuevas_imagenes'];
    $total = count($imagenes['name']);

    // Limitar a máximo 2 imágenes por producto
    $res = $conexion->query("SELECT COUNT(*) as count FROM imagenes_productos WHERE id_producto=$id_producto");
    $current = $res->fetch_assoc()['count'];

    for ($i = 0; $i < $total; $i++) {
        if ($current >= 2) break; // no permitir más de 2
        $tmp = $imagenes['tmp_name'][$i];
        $name = basename($imagenes['name'][$i]);
        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

        if (!in_array($ext, $allowed)) continue;

        $newName = 'uploads/productos/prod_' . uniqid() . '.' . $ext;
        $destino = __DIR__ . '/../../' . $newName;

        if (move_uploaded_file($tmp, $destino)) {
            $stmtImg = $conexion->prepare("INSERT INTO imagenes_productos (id_producto, ruta) VALUES (?, ?)");
            $stmtImg->bind_param("is", $id_producto, $newName);
            $stmtImg->execute();
            $current++;
        }
    }
}

echo json_encode(['status' => 'success', 'message' => 'Producto actualizado correctamente']);
exit;
?>

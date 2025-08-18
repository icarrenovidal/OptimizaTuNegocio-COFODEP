<?php
session_start();
include __DIR__ . '/../../Config/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 1. Recibir y sanitizar datos
    $nombre = $conexion->real_escape_string($_POST['nombre']);
    $id_categoria = intval($_POST['id_categoria']);
    $descripcion = $conexion->real_escape_string($_POST['descripcion']);

    // Unidad de medida personalizada si existe
    $unidad_medida = '';
    if (!empty($_POST['unidad_medida'])) {
        $unidad_medida = $conexion->real_escape_string($_POST['unidad_medida']);
    } elseif (!empty($_POST['unidad_medida_select'])) {
        $unidad_medida = $conexion->real_escape_string($_POST['unidad_medida_select']);
    } else {
        $unidad_medida = 'Sin unidad'; // valor por defecto si no llega nada
    }


    $precio_venta = floatval($_POST['precio_venta']);
    $costo_unitario = !empty($_POST['costo_unitario']) ? floatval($_POST['costo_unitario']) : null;
    $codigo_lote = !empty($_POST['codigo_lote']) ? $conexion->real_escape_string($_POST['codigo_lote']) : null;
    $cantidad_inicial = intval($_POST['cantidad_inicial']);
    $fecha_vencimiento = !empty($_POST['fecha_vencimiento']) ? $conexion->real_escape_string($_POST['fecha_vencimiento']) : null;

    // Carpeta para subir imágenes
    $uploadDir = __DIR__ . '/../../uploads/productos/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // 2. Insertar producto
    $sql_producto = "INSERT INTO productos (id_categoria, nombre, descripcion, unidad_medida, estado) 
                     VALUES ($id_categoria, '$nombre', '$descripcion', '$unidad_medida', 1)";
    if ($conexion->query($sql_producto)) {
        $id_producto = $conexion->insert_id;

        // 3. Insertar lote
        $fecha_ingreso = date('Y-m-d');
        $sql_lote = "INSERT INTO lotes (id_producto, codigo_lote, cantidad_inicial, cantidad_actual, fecha_ingreso, fecha_vencimiento)
                     VALUES ($id_producto, " . ($codigo_lote ? "'$codigo_lote'" : "NULL") . ", $cantidad_inicial, $cantidad_inicial, '$fecha_ingreso', " . ($fecha_vencimiento ? "'$fecha_vencimiento'" : "NULL") . ")";
        if ($conexion->query($sql_lote)) {
            $id_lote = $conexion->insert_id;

            // 4. Insertar precio
            $fecha_inicio = date('Y-m-d');
            $sql_precio = "INSERT INTO precios_productos (id_producto, costo_unitario, precio_venta, fecha_inicio)
                          VALUES ($id_producto, " . ($costo_unitario !== null ? $costo_unitario : "NULL") . ", $precio_venta, '$fecha_inicio')";
            $conexion->query($sql_precio);

            // 5. Registrar movimiento inicial de stock
            $sql_movimiento = "INSERT INTO movimientos_stock (id_lote, id_producto, tipo, cantidad, fecha, observacion, origen)
                               VALUES ($id_lote, $id_producto, 'ingreso', $cantidad_inicial, '$fecha_ingreso', 'Ingreso inicial', 'Sistema')";
            $conexion->query($sql_movimiento);

            // 6. Subir imágenes
            if (!empty($_FILES['imagenes']['name'][0])) {
                foreach ($_FILES['imagenes']['tmp_name'] as $key => $tmp_name) {
                    $fileName = basename($_FILES['imagenes']['name'][$key]);
                    $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
                    $newName = uniqid('prod_') . '.' . $fileExt;
                    $targetPath = $uploadDir . $newName;

                    // Validar tipo de archivo
                    $allowedTypes = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
                    if (in_array(strtolower($fileExt), $allowedTypes)) {
                        if (move_uploaded_file($tmp_name, $targetPath)) {
                            // Guardar ruta en DB (ruta relativa)
                            $rutaDB = 'uploads/productos/' . $newName;
                            $sql_imagen = "INSERT INTO imagenes_productos (id_producto, ruta) VALUES ($id_producto, '$rutaDB')";
                            $conexion->query($sql_imagen);
                        }
                    }
                }
            }

            echo "Producto agregado correctamente.";
        } else {
            echo "Error al agregar lote: " . $conexion->error;
        }
    } else {
        echo "Error al agregar producto: " . $conexion->error;
    }
} else {
    echo "Método no permitido";
}

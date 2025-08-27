<?php
session_start();
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

include __DIR__ . "/../../Config/conexion.php";

// Solo administradores pueden configurar su emprendimiento
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'administrador') {
    echo json_encode([
        "success" => false,
        "message" => "No tienes permisos para configurar un emprendimiento."
    ]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id_usuario = $_SESSION['id_usuario'] ?? null;
    $nombre = trim($_POST['nombre'] ?? '');
    $rubro = trim($_POST['rubro'] ?? '');
    $comuna = trim($_POST['comuna'] ?? '');
    $tipo_producto = trim($_POST['tipo_producto'] ?? '');

    // Validación básica
    if (empty($id_usuario)) {
        echo json_encode(["success" => false, "message" => "Usuario no identificado."]);
        exit;
    }
    if (empty($nombre)) {
        echo json_encode(["success" => false, "message" => "El nombre del emprendimiento es obligatorio."]);
        exit;
    }
    if (strlen($nombre) > 100) {
        echo json_encode(["success" => false, "message" => "El nombre del emprendimiento es demasiado largo (máx. 100 caracteres)."]);
        exit;
    }

    // Verificar si ya tiene un emprendimiento creado
    $check = $conexion->prepare("SELECT id_emprendimiento FROM emprendimientos WHERE id_usuario = ?");
    $check->bind_param("i", $id_usuario);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        echo json_encode(["success" => false, "message" => "Ya tienes un emprendimiento creado."]);
        $check->close();
        exit;
    }
    $check->close();

    // Manejo del logo
    $logo_path = null;
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['logo']['tmp_name'];
        $fileName = basename($_FILES['logo']['name']);
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($fileExt, $allowed)) {
            echo json_encode(["success" => false, "message" => "Formato de logo no permitido."]);
            exit;
        }

        $newFileName = "logo_" . $id_usuario . "_" . time() . "." . $fileExt;
        $uploadDir = __DIR__ . "/../../uploads/logos/";

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $destPath = $uploadDir . $newFileName;

        if (!move_uploaded_file($fileTmpPath, $destPath)) {
            echo json_encode(["success" => false, "message" => "Error al subir el logo."]);
            exit;
        }

        // Guardamos la ruta relativa
        $logo_path = "uploads/logos/" . $newFileName;
    }

    // Insertar emprendimiento
    $stmt = $conexion->prepare("
        INSERT INTO emprendimientos (id_usuario, nombre, logo, rubro, comuna, tipo_producto)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    if (!$stmt) {
        error_log("Error en prepare: " . $conexion->error);
        echo json_encode(["success" => false, "message" => "Error interno del servidor."]);
        exit;
    }

    $stmt->bind_param("isssss", $id_usuario, $nombre, $logo_path, $rubro, $comuna, $tipo_producto);

    if ($stmt->execute()) {
        $id_emprendimiento = $stmt->insert_id; // id del nuevo emprendimiento

        // Marcar setup como completo
        $update = $conexion->prepare("UPDATE usuarios SET setup_completo = 1 WHERE id_usuario = ?");
        $update->bind_param("i", $id_usuario);
        $update->execute();
        $update->close();

        // 🔹 Obtener datos del usuario
        $stmtUser = $conexion->prepare("SELECT nombre, apellido, email FROM usuarios WHERE id_usuario = ?");
        $stmtUser->bind_param("i", $id_usuario);
        $stmtUser->execute();
        $user = $stmtUser->get_result()->fetch_assoc();
        $stmtUser->close();

        // 🔹 Guardar en sesión
        $_SESSION['emprendimiento_id'] = $id_emprendimiento;
        $_SESSION['emprendimiento_nombre'] = $nombre;
        $_SESSION['emprendimiento_logo'] = $logo_path;

        if ($user) {
            $_SESSION['usuario_nombre'] = $user['nombre'];
            $_SESSION['usuario_apellido'] = $user['apellido'];
            $_SESSION['usuario_email'] = $user['email'];
        }

        echo json_encode([
            "success" => true,
            "message" => "Emprendimiento creado correctamente.",
            "redirect" => "/OptimizaTuNegocio/OptimizaTuNegocio/Pages/administracion/home_administracion.php"
        ]);
    } else {
        error_log("Error al crear emprendimiento: " . $stmt->error);
        echo json_encode(["success" => false, "message" => "Error al crear el emprendimiento."]);
    }

    $stmt->close();
    $conexion->close();
} else {
    echo json_encode(["success" => false, "message" => "Método no permitido."]);
}

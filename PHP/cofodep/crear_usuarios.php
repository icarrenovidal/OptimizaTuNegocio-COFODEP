<?php
session_start();
header('Content-Type: application/json');
include __DIR__ . "/../../Config/conexion.php";

// Solo cofodep puede crear usuarios
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'cofodep') {
    echo json_encode(["success" => false, "message" => "No tienes permisos para crear usuarios."]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Sanitizar y validar entradas
    $nombre = trim($_POST['nombre'] ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $rol = trim($_POST['rol'] ?? '');
    $estado = trim($_POST['estado'] ?? 'pendiente');

    // Validar campos obligatorios
    if (empty($nombre) || empty($apellido) || empty($email) || empty($password) || empty($rol)) {
        echo json_encode(["success" => false, "message" => "Todos los campos son obligatorios."]);
        exit;
    }

    // Validar formato de email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["success" => false, "message" => "Email inválido."]);
        exit;
    }

    // Validar rol permitido
    $rolesPermitidos = ['emprendedor', 'mayorista', 'administrador', 'cofodep'];
    if (!in_array($rol, $rolesPermitidos)) {
        echo json_encode(["success" => false, "message" => "Rol no válido."]);
        exit;
    }

    // Validar estado permitido
    $estadosPermitidos = ['pendiente', 'activo', 'suspendido'];
    if (!in_array($estado, $estadosPermitidos)) {
        echo json_encode(["success" => false, "message" => "Estado no válido."]);
        exit;
    }

    // Verificar si email ya existe
    $stmt = $conexion->prepare("SELECT id_usuario FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    if ($resultado && $resultado->num_rows > 0) {
        echo json_encode(["success" => false, "message" => "El email ya está registrado."]);
        exit;
    }
    $stmt->close();

    // Insertar usuario
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conexion->prepare("INSERT INTO usuarios (nombre, apellido, email, password_hash, rol, estado) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $nombre, $apellido, $email, $password_hash, $rol, $estado);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Usuario creado correctamente."]);
    } else {
        error_log("Error al crear usuario: " . $stmt->error);
        echo json_encode(["success" => false, "message" => "Error al crear el usuario."]);
    }

    $stmt->close();
    $conexion->close();
} else {
    echo json_encode(["success" => false, "message" => "Método no permitido."]);
}
?>
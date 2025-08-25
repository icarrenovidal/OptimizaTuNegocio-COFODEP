<?php
include __DIR__ . '/../Config/conexion.php';

$nombre = 'COFODEP';
$apellido = 'Admin';
$email = 'cofodep@correo.com';
$password = 'Vidal.c1'; // Cambiar
$rol = 'cofodep';
$estado = 'activo';

// Encriptar contraseña
$password_hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conexion->prepare("INSERT INTO usuarios (nombre, apellido, email, password_hash, rol, estado) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssss", $nombre, $apellido, $email, $password_hash, $rol, $estado);

if ($stmt->execute()) {
    echo "Usuario COFODEP creado correctamente.";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conexion->close();
?>

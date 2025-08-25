<?php
session_start();
header('Content-Type: application/json');

error_reporting(E_ALL);
ini_set('display_errors', 1);

include __DIR__ . "/../../Config/conexion.php"; // Verifica bien esta ruta

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    $stmt = $conexion->prepare("SELECT id_usuario, nombre, apellido, email, password_hash, rol, estado 
                                FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado && $resultado->num_rows === 1) {
        $usuario = $resultado->fetch_assoc();

        if ($usuario['estado'] !== 'activo') {
            echo json_encode(["success" => false, "message" => "Tu cuenta está " . $usuario['estado']]);
            exit;
        }

        if (password_verify($password, $usuario['password_hash'])) {
            $_SESSION['id_usuario'] = $usuario['id_usuario'];
            $_SESSION['nombre'] = $usuario['nombre'];
            $_SESSION['rol'] = $usuario['rol'];

            switch ($usuario['rol']) {
                case 'cofodep':
                    $redirect = "/OptimizaTuNegocio/OptimizaTuNegocio/Pages/cofodep/home_cofodep.php";
                    break;
                case 'administrador':
                    $redirect = "/OptimizaTuNegocio/OptimizaTuNegocio/auth/Pages/administracion/home_administracion.php";
                    break;
                case 'emprendedor':
                    $redirect = "/OptimizaTuNegocio/OptimizaTuNegocio/auth/Pages/emprendedores/home_emprendedor.php";
                    break;
                default:
                    $redirect = "/OptimizaTuNegocio/OptimizaTuNegocio/auth/Pages/error/no_role.php";
            }

            echo json_encode(["success" => true, "redirect" => $redirect]);
        } else {
            echo json_encode(["success" => false, "message" => "Contraseña incorrecta"]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Usuario no encontrado"]);
    }

    $stmt->close();
    $conexion->close();
}
?>

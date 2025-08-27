<?php
session_start();
header('Content-Type: application/json');

error_reporting(E_ALL);
ini_set('display_errors', 1);

include __DIR__ . "/../../Config/conexion.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Traemos setup_completo también
    $stmt = $conexion->prepare("SELECT id_usuario, nombre, apellido, email, password_hash, rol, estado, setup_completo 
                                FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado && $resultado->num_rows === 1) {
        $usuario = $resultado->fetch_assoc();

        // Validar estado
        if ($usuario['estado'] !== 'activo') {
            echo json_encode(["success" => false, "message" => "Tu cuenta está " . $usuario['estado']]);
            exit;
        }

        // Validar password
        if (password_verify($password, $usuario['password_hash'])) {
            // Guardar sesión básica
            $_SESSION['id_usuario'] = $usuario['id_usuario'];
            $_SESSION['usuario_nombre'] = $usuario['nombre'];
            $_SESSION['usuario_apellido'] = $usuario['apellido'];
            $_SESSION['rol'] = $usuario['rol'];

            $redirect = '';

            switch ($usuario['rol']) {
                case 'cofodep':
                    $redirect = "/OptimizaTuNegocio/OptimizaTuNegocio/Pages/cofodep/home_cofodep.php";
                    break;

                case 'administrador':
                    if ((int)$usuario['setup_completo'] === 0) {
                        // No ha configurado su emprendimiento aún
                        $redirect = "/OptimizaTuNegocio/OptimizaTuNegocio/Pages/administracion/setup_emprendimiento.php";
                    } else {
                        // Traer datos del emprendimiento para navbar
                        $stmtEmp = $conexion->prepare("SELECT id_emprendimiento, nombre, logo 
                                                       FROM emprendimientos 
                                                       WHERE id_usuario = ? LIMIT 1");
                        $stmtEmp->bind_param("i", $usuario['id_usuario']);
                        $stmtEmp->execute();
                        $resEmp = $stmtEmp->get_result();

                        if ($resEmp && $resEmp->num_rows > 0) {
                            $emp = $resEmp->fetch_assoc();
                            $_SESSION['id_emprendimiento'] = $emp['id_emprendimiento'];
                            $_SESSION['emprendimiento_nombre'] = $emp['nombre'];
                            $_SESSION['emprendimiento_logo'] = $emp['logo'] ?: './../../IMG/default_logo.png';
                        }
                        $stmtEmp->close();

                        // Redirigir a home de administración
                        $redirect = "/OptimizaTuNegocio/OptimizaTuNegocio/Pages/administracion/home_administracion.php";
                    }
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

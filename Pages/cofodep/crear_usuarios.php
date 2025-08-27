<?php
include './../../Config/auth_check.php';
include './../../PHP/cofodep/navbar_cofodep.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HOME</title>
    <!-- Bootstrap primero -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="./../../CSS/estilos_emprendedores.css">
    <link rel="stylesheet" href="./../../CSS/utilities.css">
    <link rel="stylesheet" href="./../../CSS/formularios.css">
    <link rel="stylesheet" href="./../../CSS/estilo_contrasena.css">
</head>

<body class="login-body">
    <div class="login-container">
        <!-- Crear Usuario -->
        <div class="container">
            <h1>Crear Nuevo Emprendimiento</h1>

            <!-- Mensajes de retroalimentación -->
            <div id="crearUsuarioError" class="alert alert-danger d-none" role="alert"></div>
            <div id="crearUsuarioSuccess" class="alert alert-success d-none" role="alert"></div>

            <form id="crearUsuarioForm" class="form-grid">
                <div class="form-group">
                    <label for="nombre">Nombre <span class="required-asterisk">*</span></label>
                    <input type="text" id="nombre" name="nombre" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="apellido">Apellido <span class="required-asterisk">*</span></label>
                    <input type="text" id="apellido" name="apellido" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="email">Email <span class="required-asterisk">*</span></label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="password">Contraseña <span class="required-asterisk">*</span></label>
                    <input type="password" id="password" name="password" class="form-control" required>
                    <small class="form-text text-muted">Mínimo 8 caracteres, con al menos un número y un carácter especial</small>
                    <div class="password-feedback">
                        <p id="length" class="invalid">❌ Mínimo 8 caracteres</p>
                        <p id="number" class="invalid">❌ Al menos un número</p>
                        <p id="special" class="invalid">❌ Al menos un carácter especial</p>
                    </div>

                </div>


                <div class="form-group">
                    <label for="rol">Rol <span class="required-asterisk">*</span></label>
                    <select id="rol" name="rol" class="form-control" required>
                        <option value="">Selecciona un rol</option>
                        <option value="emprendedor">Emprendedor</option>
                        <option value="mayorista">Mayorista</option>
                        <option value="administrador">Administrador</option>
                        <option value="cofodep">Cofodep</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="estado">Estado</label>
                    <select id="estado" name="estado" class="form-control">
                        <option value="pendiente" selected>Pendiente</option>
                        <option value="activo">Activo</option>
                        <option value="suspendido">Suspendido</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary full-width">Crear Emprendimiento</button>
            </form>
        </div>
    </div>

    <script src="./../../JS/crear_usuarios.js"></script>
</body>

</html>
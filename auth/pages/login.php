<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Optimiza Tu Negocio</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="./../../CSS/estilos_emprendedores.css">
    <link rel="stylesheet" href="./../../CSS/login.css">

</head>

<body class="login-body">
    <div class="login-container">
        <div class="login-header">
            <div class="login-icon">
                <i class="fas fa-store"></i>
            </div>
            <h1>Optimiza Tu Negocio</h1>
            <p>Ingresa a tu cuenta para continuar</p>
        </div>


        <div class="login-body-container">
            <form id="loginForm">
                <!-- BLOQUE DE ERRORES  -->
                <div id="loginError" class="login-error" style="display:none">
                    <i class="fas fa-exclamation-circle"></i>
                    <span id="loginErrorMessage"></span>
                </div>


                <div class="login-form-group">
                    <label for="email">Correo electrónico</label>
                    <input type="email" id="email" name="email" class="login-form-control" placeholder="tucorreo@email.com" required>
                </div>

                <div class="login-form-group">
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" class="login-form-control" placeholder="Ingresa tu contraseña" required>

                </div>
                <div class="remember-me">
                    <input type="checkbox" id="remember">
                    <label for="remember">Recordar mi sesión</label>
                </div>

                <button type="submit" class="btn-login">Iniciar Sesión</button>



                <div class="login-links">
                    <a href="#">¿Olvidaste tu contraseña?</a>
                    <a href="#">Crear una cuenta</a>
                </div>
            </form>
        </div>
    </div>
    <script src="./../JS/login.js"></script>
</body>

</html>
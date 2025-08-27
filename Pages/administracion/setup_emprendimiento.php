<?php
include './../../Config/auth_check.php';
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
</head>
<body>
    <div class="container">
        <h1>Configura tu Emprendimiento</h1>

        <div id="setupError" class="alert alert-danger d-none"></div>
        <div id="setupSuccess" class="alert alert-success d-none"></div>

        <form id="setupForm" class="form-grid" enctype="multipart/form-data">
            <div class="form-group full-width">
                <label for="nombre">Nombre del Emprendimiento <span class="required-asterisk">*</span></label>
                <input type="text" id="nombre" name="nombre" class="form-control" required>
            </div>

            <div class="form-group full-width">
                <label for="logo">Logo del Emprendimiento</label>
                <div class="file-upload" id="fileUpload">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <p>Haz clic o arrastra tu logo aquí</p>
                    <input type="file" id="logo" name="logo" class="form-control" accept="image/*" hidden>
                </div>
                <div class="preview-container" id="previewContainer"></div>
            </div>

            <div class="form-group">
                <label for="rubro">Rubro</label>
                <input type="text" id="rubro" name="rubro" class="form-control">
            </div>

            <div class="form-group">
                <label for="comuna">Comuna / Ciudad</label>
                <input type="text" id="comuna" name="comuna" class="form-control">
            </div>

            <div class="form-group full-width">
                <label for="tipo_producto">Tipo de Producto / Servicio</label>
                <textarea id="tipo_producto" name="tipo_producto" class="form-control"></textarea>
            </div>

            <button type="submit" class="btn btn-primary full-width">Guardar Emprendimiento</button>
        </form>
    </div>

    <script src="./../../JS/administradores/setup_emprendimiento.js"></script>
</body>
</html>
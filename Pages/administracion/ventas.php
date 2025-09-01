<?php
include __DIR__ . '/../../Config/auth_check.php';
include __DIR__ . '/../../PHP/administracion/navbar.php';

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ventas</title>
    <!-- Bootstrap primero -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="./../../CSS/estilos_emprendedores.css">
    <link rel="stylesheet" href="./../../CSS/utilities.css">
    <link rel="stylesheet" href="./../../CSS/formularios.css">
</head>

<body>
    <div class="container">
        <h1>Ventas</h1>

        <!-- Filtros -->
        <div class="form-grid mb-4">
            <div class="form-group">
                <label>Fecha desde</label>
                <input type="date" id="filtro-fecha-desde" class="form-control">
            </div>
            <div class="form-group">
                <label>Fecha hasta</label>
                <input type="date" id="filtro-fecha-hasta" class="form-control">
            </div>
        </div>

        <!-- Tabla de ventas -->
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="bg-light">
                    <tr>
                        <th>ID Venta</th>
                        <th>Fecha</th>
                        <th>Total</th>
                        <th>Canal</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="ventas-body">
                    <tr>
                        <td colspan="5" class="text-center">Cargando ventas...</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-between align-center mt-3">
            <button id="prev-page" class="btn btn-secondary btn-sm">Anterior</button>
            <span id="page-info">Página 1</span>
            <button id="next-page" class="btn btn-secondary btn-sm">Siguiente</button>
        </div>

    </div>


    <script src="./../../JS/ventas.js"></script>

</body>

</html>
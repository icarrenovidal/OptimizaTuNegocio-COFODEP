<?php
include './../../Config/auth_check.php';
include './../../PHP/cofodep/navbar_cofodep.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <!-- Bootstrap primero -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="./../../CSS/estilos_emprendedores.css">
    <link rel="stylesheet" href="./../../CSS/utilities.css">
    <link rel="stylesheet" href="./../../CSS/formularios.css">
</head>

<body>
<div class="container py-4">
    <h1 class="mb-4">Dashboard Cofodep</h1>

    <!-- Contadores -->
    <div class="row mb-4">
        <div class="col-6 col-md-3 mb-3">
            <div class="card text-center p-3">
                <div class="fw-bold">Usuarios activos</div>
                <div id="usuarios-activos">0</div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="card text-center p-3">
                <div class="fw-bold">Emprendimientos activos</div>
                <div id="emprendimientos-activos">0</div>
            </div>
        </div>
    </div>

    <!-- Evolución lado a lado -->
    <div class="row g-4 mb-4">
        <div class="col-12 col-lg-6">
            <div class="card">
                <div class="card-header fw-bold">Evolución de usuarios</div>
                <div class="card-body">
                    <canvas id="grafico-usuarios-evolucion"></canvas>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-6">
            <div class="card">
                <div class="card-header fw-bold">Evolución de emprendimientos</div>
                <div class="card-body">
                    <canvas id="grafico-emprendimientos-evolucion"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Roles predominantes -->
    <div class="row g-4 mb-4">
        <div class="col-12 col-lg-6">
            <div class="card">
                <div class="card-header fw-bold">Roles predominantes</div>
                <div class="card-body">
                    <canvas id="grafico-roles"></canvas>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- JS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="./../../JS/cofodep/dashboard.js"></script>
<script src="./../../JS/cofodep/dashboard_usuarios_activos.js"></script>
<script src="./../../JS/cofodep/dashboard_emprendimientos_activos.js"></script>
<script src="./../../JS/cofodep/usuarios_evolucion.js"></script>
<script src="./../../JS/cofodep/emprendimientos_evolucion.js"></script>
<script src="./../../JS/cofodep/roles_predominantes.js"></script>

<!-- Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>


</html>
<?php
include __DIR__ . '/../../Config/auth_check.php';
include __DIR__ . '/../../PHP/administracion/navbar.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard de Ventas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="./../../CSS/estilos_emprendedores.css">
    <link rel="stylesheet" href="./../../CSS/utilities.css">
    <link rel="stylesheet" href="./../../CSS/formularios.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="container py-4">
    <h1 class="mb-4">Dashboard de Ventas</h1>

    <!-- Filtros -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <label class="form-label">Vista</label>
            <select id="tipo-grafico" class="form-select">
                <option value="dia">Por día</option>
                <option value="mes">Por mes</option>
            </select>
        </div>
    </div>

    <!-- Tarjetas resumen -->
    <div class="row mb-4" id="resumen-tarjetas">
        <div class="col-6 col-md-3 mb-3">
            <div class="card text-center p-2">
                <div class="fw-bold">Hoy</div>
                <div id="total-hoy">$0</div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="card text-center p-2">
                <div class="fw-bold">Ayer</div>
                <div id="total-ayer">$0</div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="card text-center p-2">
                <div class="fw-bold">Este mes</div>
                <div id="total-este-mes">$0</div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="card text-center p-2">
                <div class="fw-bold">Mes anterior</div>
                <div id="total-mes-anterior">$0</div>
            </div>
        </div>
    </div>

    <!-- Gráfico de ventas -->
    <div class="card">
        <div class="card-body">
            <canvas id="grafico-ventas"></canvas>
        </div>
    </div>
</div>

<!-- JS -->
<script src="./../../JS/dashboard.js"></script>
</body>
</html>

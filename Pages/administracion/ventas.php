<?php
include __DIR__ . '/../../Config/auth_check.php';
include __DIR__ . '/../../PHP/administracion/navbar.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ventas</title>
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="./../../CSS/estilos_emprendedores.css">
    <link rel="stylesheet" href="./../../CSS/utilities.css">
    <link rel="stylesheet" href="./../../CSS/formularios.css">

    <style>
        /* Estilos responsive para tabla tipo cards en móviles */
        @media (max-width: 768px) {
            table thead {
                display: none;
            }

            table tbody tr {
                display: block;
                margin-bottom: 1rem;
                border: 1px solid #ddd;
                border-radius: 8px;
                padding: 0.5rem;
                background: #fff;
            }

            table tbody td {
                display: flex;
                justify-content: space-between;
                padding: 0.5rem;
                border: none !important;
            }

            table tbody td::before {
                content: attr(data-label);
                font-weight: bold;
                margin-right: 1rem;
                color: #555;
            }
        }

        /* Paginación responsive */
        .pagination-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: .5rem;
            text-align: center;
        }

        @media (min-width: 768px) {
            .pagination-container {
                flex-direction: row;
                justify-content: center;
            }

            .pagination-container #page-info {
                margin-left: 1rem;
            }
        }
    </style>
</head>

<body>
    <div class="container py-4">
        <h1 class="mb-4">Ventas</h1>

        <!-- Filtros -->
        <div class="row g-3 mb-4">
            <div class="col-12 col-md-3">
                <label class="form-label">Fecha desde</label>
                <input type="date" id="filtro-fecha-desde" class="form-control">
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label">Fecha hasta</label>
                <input type="date" id="filtro-fecha-hasta" class="form-control">
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label">Canal de venta</label>
                <select id="filtro-canal" class="form-select">
                    <option value="">Todos</option>
                    <option value="ferias">Ferias</option>
                    <option value="redes sociales">Redes Sociales</option>
                    <option value="tienda física">Tienda Física</option>
                    <option value="otro">Otro</option>
                </select>
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label">Método de pago</label>
                <select id="filtro-metodo" class="form-select">
                    <option value="">Todos</option>
                    <option value="tarjeta_credito">Tarjeta de crédito</option>
                    <option value="tarjeta_debito">Tarjeta de débito</option>
                    <option value="efectivo">Efectivo</option>
                    <option value="transferencia">Transferencia</option>
                    <option value="otro">Otro</option>
                </select>
            </div>
        </div>

        <!-- Tabla de ventas -->
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead class="bg-light">
                    <tr>
                        <th>ID Venta</th>
                        <th>Fecha</th>
                        <th>Total</th>
                        <th>Canal</th>
                        <th>Método de Pago</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="ventas-body">
                    <tr>
                        <td colspan="6" class="text-center">Cargando ventas...</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Contenedor de paginación dinámica -->
        <!-- Contenedor de paginación dinámica -->
        <div class="pagination-container mt-3">
            <div id="pagination-wrap" class="mb-2"></div>
            <div id="page-info" class="small text-muted"></div>
        </div>

    </div>

    <!-- JS -->
    <script src="./../../JS/ventas.js"></script>
</body>

</html>
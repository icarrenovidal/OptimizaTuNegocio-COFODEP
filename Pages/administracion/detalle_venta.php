<?php
include __DIR__ . '/../../Config/auth_check.php';
include __DIR__ . '/../../PHP/administracion/navbar.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de Venta</title>
    <!-- Bootstrap y FontAwesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Estilos globales -->
    <link rel="stylesheet" href="./../../CSS/estilos_emprendedores.css">

    <link rel="stylesheet" href="./../../CSS/boleta.css">

</head>

<body>
    <div class="container">
        <div class="tabla-boleta" id="boleta">
            <h2>Detalle de Venta</h2>
            <div class="venta-info mb-3">
                <p><strong>Fecha:</strong> <span id="venta-fecha"></span></p>
                <p><strong>Canal:</strong> <span id="venta-canal"></span></p>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Precio Unitario</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody id="detalle-body">
                    <tr>
                        <td colspan="4" class="text-center">Cargando detalle...</td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="total">Total:</td>
                        <td id="venta-total">$0</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <script src="./../../JS/detalle_venta.js"></script>

</body>

</html>
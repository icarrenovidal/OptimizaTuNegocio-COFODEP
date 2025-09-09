<?php
include __DIR__ . '/../../Config/auth_check.php';
include __DIR__ . '/../../PHP/administracion/navbar.php';

// Obtener id del producto desde la URL
$id_producto = isset($_GET['id']) ? intval($_GET['id']) : 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Descontar Producto</title>
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="./../../CSS/estilos_emprendedores.css">
    <link rel="stylesheet" href="./../../CSS/utilities.css">
    <link rel="stylesheet" href="./../../CSS/formularios.css">
</head>
<body>
<div class="container mt-4">
    <h2 class="mb-4">
        <i class="fas fa-minus-circle text-danger me-2"></i>
        Descontar Mercadería
    </h2>

    <div class="card shadow rounded-3">
        <div class="card-body">
            <form id="formDescontarProducto">
                <!-- ID oculto -->
                <input type="hidden" id="id_producto" name="id_producto" value="<?php echo $id_producto; ?>">

                <!-- Nombre producto (solo lectura) -->
                <div class="mb-3">
                    <label for="nombreProducto" class="form-label">Producto</label>
                    <input type="text" id="nombreProducto" class="form-control" disabled>
                </div>

                <!-- Selección de lote -->
                <div class="mb-3">
                    <label for="id_lote" class="form-label">Lote</label>
                    <select id="id_lote" name="id_lote" class="form-select">
                        <option value="">-- Seleccione un lote --</option>
                        <!-- Aquí se llenará con JS -->
                    </select>
                </div>

                <!-- Cantidad a descontar -->
                <div class="mb-3">
                    <label for="cantidad" class="form-label">Cantidad a descontar</label>
                    <input type="number" id="cantidad" name="cantidad" class="form-control" min="1" required>
                </div>

                <!-- Origen -->
                <div class="mb-3">
                    <label for="origen" class="form-label">Motivo / Origen</label>
                    <select id="origen" name="origen" class="form-select" required>
                        <option value="">-- Seleccione motivo --</option>
                        <option value="venta">Venta</option>
                        <option value="devolucion">Devolución</option>
                        <option value="ajuste_inventario">Ajuste de Inventario</option>
                    </select>
                </div>

                <!-- Observación -->
                <div class="mb-3">
                    <label for="observacion" class="form-label">Observación</label>
                    <textarea id="observacion" name="observacion" class="form-control" rows="3" placeholder="Detalle del descuento (opcional)"></textarea>
                </div>

                <!-- Botones -->
                <div class="d-flex justify-content-between">
                    <a href="detalle_producto.php?id=<?php echo $id_producto; ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Volver
                    </a>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-check me-1"></i> Confirmar Descuento
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bootstrap y JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="./../../JS/descontar_productos.js"></script>
</body>
</html>

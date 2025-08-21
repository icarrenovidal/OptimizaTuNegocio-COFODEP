<?php
session_start();
include __DIR__ . '/../../PHP/administracion/navbar.php';
include __DIR__ . '/../../PHP/administracion/obtener_producto_stock.php';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Stock</title>
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="./../../CSS/estilos_emprendedores.css">
    <link rel="stylesheet" href="./../../CSS/utilities.css">
    <link rel="stylesheet" href="./../../CSS/formularios.css">
</head>

<body>
    <div class="container mt-4">
        <h1 class="d-flex align-items-center gap-2 mb-4">
            <i class="fas fa-boxes" style="color: var(--color4);"></i>
            Agregar Stock a Producto
        </h1>
        <a href="ver_productos.php" class="btn btn-secondary mb-3">
            <i class="fas fa-arrow-left me-1"></i> Volver al listado
        </a>

        <?php if ($producto): ?>
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-body p-3">
                    <div class="d-flex flex-column flex-md-row align-items-center gap-3">
                        <?php if (!empty($producto['imagenes'][0])): ?>
                            <div class="flex-shrink-0">
                                <img src="<?= $producto['imagenes'][0] ?>"
                                    class="img-fluid rounded"
                                    alt="<?= htmlspecialchars($producto['nombre']) ?>"
                                    style="max-height: 120px; width: auto;">
                            </div>
                        <?php endif; ?>

                        <div class="flex-grow-1">
                            <div class="d-flex flex-column">
                                <h5 class="card-title mb-2"><?= htmlspecialchars($producto['nombre']) ?></h5>

                                <div class="d-flex flex-column flex-sm-row align-items-sm-center gap-2 gap-sm-3">
                                    <span class="text-muted">Stock actual:</span>
                                    <strong class="<?= $producto['stock'] < 5 ? 'text-danger' : 'text-success' ?>">
                                        <?= $producto['stock'] ?> <?= htmlspecialchars($producto['unidad_medida']) ?>
                                    </strong>

                                    <?php if ($producto['stock'] < 5): ?>
                                        <span class="badge bg-warning text-dark">
                                            <i class="fas fa-exclamation-triangle me-1"></i> Stock bajo
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <?php
            // Si no viene id_producto por URL, obtenemos todos los productos
            $res = $conexion->query("SELECT id_producto, nombre, unidad_medida FROM productos ORDER BY nombre ASC");
            $productos = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
            ?>

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-3">Seleccionar producto</h5>
                    <p class="text-muted">Selecciona un producto de la lista para agregar stock.</p>
                    <!-- Aquí iría el selector de productos si es necesario -->
                </div>
            </div>
        <?php endif; ?>

        <form id="form-stock" action="./../../PHP/administracion/procesar_agregar_stock.php" method="POST">
            <!-- Si viene id_producto por URL -->
            <?php if ($producto): ?>
                <input type="hidden" name="id_producto" value="<?= $producto['id_producto'] ?>">
            <?php else: ?>
                <div class="form-section full-width mb-3">
                    <h2 class="section-title"><i class="fas fa-cube"></i> Seleccionar Producto</h2>
                    <div class="form-group">
                        <label for="id_producto">Producto <span class="required-asterisk">*</span></label>
                        <select class="form-select" id="id_producto" name="id_producto" required>
                            <option value="">Seleccione un producto</option>
                            <?php foreach ($productos as $prod): ?>
                                <option value="<?= $prod['id_producto'] ?>"><?= htmlspecialchars($prod['nombre']) ?> (<?= htmlspecialchars($prod['unidad_medida']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            <?php endif; ?>

            <div class="form-section full-width mb-3">
                <h2 class="section-title"><i class="fas fa-warehouse"></i> Información de Lote</h2>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="codigo_lote">Código de Lote</label>
                            <input type="text" id="codigo_lote" name="codigo_lote" placeholder="Opcional (ej: LOTE-2025-001)">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="cantidad">Cantidad a ingresar <span class="required-asterisk">*</span></label>
                            <input type="number" id="cantidad" name="cantidad" min="1" required>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="fecha_ingreso">Fecha de ingreso</label>
                            <input type="date" id="fecha_ingreso" name="fecha_ingreso" value="<?= date('Y-m-d') ?>" max="<?= date('Y-m-d') ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="fecha_vencimiento">Fecha de vencimiento</label>
                            <input type="date" id="fecha_vencimiento" name="fecha_vencimiento">
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center mt-3">
                <button type="submit" class="btn-submit">
                    <i class="fas fa-save"></i> Guardar Stock
                </button>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('form-stock').addEventListener('submit', function(e) {
            const requiredFields = this.querySelectorAll('[required]');
            let isValid = true;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.style.borderColor = 'var(--color5)';
                    isValid = false;
                } else {
                    field.style.borderColor = 'var(--color3)';
                }
            });

            if (!isValid) {
                e.preventDefault();
                alert('Por favor complete todos los campos requeridos');
            }
        });
    </script>
</body>

</html>
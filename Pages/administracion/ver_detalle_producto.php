<?php
session_start();
include __DIR__ . '/../../PHP/administracion/navbar.php';
include __DIR__ . '/../../PHP/administracion/obtener_detalle_producto.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle Producto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="./../../CSS/estilos_emprendedores.css">
    <link rel="stylesheet" href="./../../CSS/utilities.css">

    <style>
        /* Galería */
        .product-gallery img {
            object-fit: cover;
        }

        .thumbs img {
            cursor: pointer;
            transition: transform 0.2s;
        }

        .thumbs img:hover {
            transform: scale(1.05);
        }
    </style>
</head>

<body>
    <div class="container mt-4">
        <a href="ver_productos.php" class="btn btn-secondary mb-3">
            <i class="fas fa-arrow-left me-1"></i> Volver al listado
        </a>

        <div class="row g-4">
            <!-- Galería de imágenes -->
            <div class="col-lg-6 col-12">
                <div class="card shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="ratio ratio-1x1 mb-3">
                            <img id="mainImage" src="<?= $producto['imagenes'][0] ?? 'https://via.placeholder.com/800?text=Sin+imagen' ?>"
                                class="rounded product-gallery w-100 h-100"
                                alt="<?= htmlspecialchars($producto['nombre_producto']) ?>">
                        </div>

                        <div class="d-flex flex-wrap gap-2 thumbs">
                            <?php foreach ($producto['imagenes'] as $index => $img): ?>
                                <div class="ratio ratio-1x1" style="width: 80px;">
                                    <img src="<?= $img ?>"
                                        class="rounded border <?= $index === 0 ? 'border-primary' : 'border-light' ?>"
                                        onclick="changeImage(this, '<?= $img ?>')">
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información del producto -->
            <div class="col-lg-6 col-12">
                <div class="card shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start flex-wrap">
                            <div>
                                <h2 class="mb-2"><?= htmlspecialchars($producto['nombre_producto']) ?></h2>
                                <span class="badge bg-secondary mb-3"><?= htmlspecialchars($producto['nombre_categoria']) ?></span>
                            </div>
                            <span class="badge bg-secondary fs-6">
                                $<?= number_format($producto['precio'], 0, '', '.') ?>
                            </span>
                        </div>

                        <div class="mb-4">
                            <h5 class="text-muted">Descripción</h5>
                            <p class="lead"><?= nl2br(htmlspecialchars($producto['descripcion'])) ?></p>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="card bg-light h-100">
                                    <div class="card-body">
                                        <h5 class="card-title text-prueba"><i class="fas fa-box-open me-2"></i>Stock</h5>

                                        <p class="fs-4 mb-0"><?= $producto['stock'] ?></p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="card bg-light h-100">
                                    <div class="card-body">
                                         <h5 class="card-title text-prueba"><i class="fas fa-ruler me-2"></i>Unidad</h5>
                                        <p class="fs-4 mb-0"><?= htmlspecialchars($producto['unidad_medida']) ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de lotes -->
        <div class="card shadow-sm mt-4">
            <div class="card-body">
                <h4 class="mb-3"><i class="fas fa-warehouse me-2"></i>Lotes del Producto</h4>

                <?php if (count($lotes) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>ID Lote</th>
                                    <th>Código Lote</th>
                                    <th>Cantidad Inicial</th>
                                    <th>Cantidad Actual</th>
                                    <th>Fecha Ingreso</th>
                                    <th>Fecha Vencimiento</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($lotes as $lote): ?>
                                    <tr>
                                        <td><?= $lote['id_lote'] ?></td>
                                        <td><?= htmlspecialchars($lote['codigo_lote']) ?></td>
                                        <td><?= $lote['cantidad_inicial'] ?></td>
                                        <td><?= $lote['cantidad_actual'] ?></td>
                                        <td><?= date('d-m-Y', strtotime($lote['fecha_ingreso'])) ?></td>
                                        <td><?= $lote['fecha_vencimiento'] ? date('d-m-Y', strtotime($lote['fecha_vencimiento'])) : '-' ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted">No hay lotes registrados para este producto.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        function changeImage(thumb, imgSrc) {
            document.getElementById('mainImage').src = imgSrc;
            document.querySelectorAll('.thumbs img').forEach(img => {
                img.classList.remove('border-primary');
                img.classList.add('border-light');
            });
            thumb.classList.remove('border-light');
            thumb.classList.add('border-primary');
        }
    </script>
</body>

</html>

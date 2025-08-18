<?php
session_start();
include __DIR__ . '/../../PHP/administracion/navbar.php';
include __DIR__ . '/../../PHP/administracion/obtener_productos.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado Productos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="./../../CSS/estilos_emprendedores.css">
    <link rel="stylesheet" href="./../../CSS/utilities.css">
    <link rel="stylesheet" href="./../../CSS/formularios.css">
    <link rel="stylesheet" href="./../../CSS/ver_productos.css">


</head>

<body>
    <div class="container mt-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
            <h2 class="mb-0"><i class="fas fa-box me-2"></i>Productos</h2>
            <div class="btn-group flex-wrap mb-2" role="group">
                <button id="view-cards" class="btn btn-outline-prueba btn-sm active" title="Vista de tarjetas">
                    <i class="fas fa-th-large me-1"></i> Cards
                </button>
                <button id="view-rows" class="btn btn-outline-pruebita btn-sm" title="Vista de lista">
                    <i class="fas fa-list me-1"></i> Filas
                </button>
            </div>
        </div>

        <!-- Vista Cards -->
        <div id="productos-cards" class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 g-4">
            <?php foreach ($productos as $prod): ?>
                <div class="col">
                    <div class="card h-100 shadow-sm">
                        <div class="img-container">
                            <img src="<?= $prod['imagenes'][0] ?? 'https://via.placeholder.com/300?text=Sin+imagen' ?>"
                                class="primary-img"
                                alt="<?= htmlspecialchars($prod['nombre_producto']) ?>">
                            <img src="<?= $prod['imagenes'][1] ?? $prod['imagenes'][0] ?? 'https://via.placeholder.com/300?text=Sin+imagen' ?>"
                                class="hover-img"
                                alt="<?= htmlspecialchars($prod['nombre_producto']) ?> (vista alternativa)">
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2 flex-wrap">
                                <h5 class="card-title mb-0"><?= htmlspecialchars($prod['nombre_producto']) ?></h5>
                                <span class="price-tag">$<?= number_format($prod['precio']) ?></span>
                            </div>
                            <span class="badge bg-secondary mb-2"><?= htmlspecialchars($prod['nombre_categoria']) ?></span>
                            <p class="card-text"><?= htmlspecialchars($prod['descripcion']) ?></p>
                        </div>
                        <div class="card-footer bg-white border-top-0 pt-0">
                            <div class="d-flex justify-content-between align-items-center flex-wrap">
                                <small class="text-muted">
                                    <span>Disponibles:</span>
                                    <span><?= $prod['stock'] ?> <?= htmlspecialchars($prod['unidad_medida']) ?></span>
                                </small>
                                <a href="ver_detalle_producto.php?id=<?= $prod['id_producto'] ?>" class="btn btn-sm btn-outline-prueba mt-1 mt-md-0">
                                    <i class="fas fa-eye me-1"></i> Detalle
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Vista Filas -->
        <div id="productos-rows" class="d-none table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Imagen</th>
                        <th>Producto</th>
                        <th>Categoría</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th>Descripción</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productos as $prod): ?>
                        <tr>
                            <td>
                                <img src="<?= $prod['imagenes'][0] ?? 'https://via.placeholder.com/50?text=Sin+imagen' ?>"
                                    class="img-thumbnail"
                                    style="width: 50px; height: 50px; object-fit: cover;"
                                    alt="<?= htmlspecialchars($prod['nombre_producto']) ?>">
                            </td>
                            <td><?= htmlspecialchars($prod['nombre_producto']) ?></td>
                            <td><?= htmlspecialchars($prod['nombre_categoria']) ?></td>
                            <td>$<?= number_format($prod['precio']) ?></td>
                            <td><?= $prod['stock'] ?> <?= htmlspecialchars($prod['unidad_medida']) ?></td>
                            <td class="small"><?= htmlspecialchars($prod['descripcion']) ?></td>
                            <td>
                                <a href="ver_detalle_producto.php?id=<?= $prod['id_producto'] ?>" class="btn btn-sm btn-outline-prueba" title="Ver detalle">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Cambio entre vista cards y filas
        const btnCards = document.getElementById('view-cards');
        const btnRows = document.getElementById('view-rows');
        const viewCards = document.getElementById('productos-cards');
        const viewRows = document.getElementById('productos-rows');

        btnCards.addEventListener('click', () => {
            viewCards.classList.remove('d-none');
            viewRows.classList.add('d-none');
            btnCards.classList.add('active');
            btnRows.classList.remove('active');
        });

        btnRows.addEventListener('click', () => {
            viewRows.classList.remove('d-none');
            viewCards.classList.add('d-none');
            btnRows.classList.add('active');
            btnCards.classList.remove('active');
        });
    </script>
</body>

</html>

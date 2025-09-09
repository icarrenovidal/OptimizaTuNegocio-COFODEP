<?php
include __DIR__ . '/../../Config/auth_check.php';
include __DIR__ . '/../../PHP/administracion/navbar.php';
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
        <a id="editarProductoBtn" href="#" class="btn btn-primary mb-3">
            <i class="fas fa-edit me-1"></i> Editar Producto
        </a>
        <a id="descontarProductoBtn" href="#" class="btn btn-danger mb-3 ms-2">
            <i class="fas fa-minus-circle me-1"></i> Descontar Mercadería
        </a>


        <div class="row g-4">
            <!-- Galería de imágenes -->
            <div class="col-lg-6 col-12">
                <div class="card shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="ratio ratio-1x1 mb-3">
                            <img id="mainImage" src="https://via.placeholder.com/800?text=Sin+imagen"
                                class="rounded product-gallery w-100 h-100" alt="Producto">
                        </div>
                        <div class="d-flex flex-wrap gap-2 thumbs"></div>
                    </div>
                </div>
            </div>

            <!-- Información del producto -->
            <div class="col-lg-6 col-12">
                <div class="card shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start flex-wrap">
                            <div>
                                <h2 class="mb-2" id="nombreProducto">Cargando...</h2>
                                <span class="badge bg-secondary mb-3" id="categoriaProducto">-</span>
                            </div>
                            <span class="badge bg-secondary fs-6" id="precioProducto">$0</span>
                        </div>

                        <div class="mb-4">
                            <h5 class="text-muted">Descripción</h5>
                            <p class="lead" id="descripcionProducto">Cargando...</p>
                            <button class="btn btn-link p-0 d-none" data-bs-toggle="modal" data-bs-target="#descripcionModal" id="btnVerMas">Ver más</button>
                        </div>

                        <!-- MODAL -->
                        <div class="modal fade" id="descripcionModal" tabindex="-1" aria-labelledby="descripcionModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="descripcionModalLabel">Descripción completa</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p class="lead" id="descripcionCompleta"></p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="card bg-light h-100">
                                    <div class="card-body">
                                        <h5 class="card-title text-prueba"><i class="fas fa-box-open me-2"></i>Stock</h5>
                                        <p class="fs-4 mb-0" id="stockProducto">0</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="card bg-light h-100">
                                    <div class="card-body">
                                        <h5 class="card-title text-prueba"><i class="fas fa-ruler me-2"></i>Unidad</h5>
                                        <p class="fs-4 mb-0" id="unidadProducto">-</p>
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
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="mb-0"><i class="fas fa-warehouse me-2"></i>Lotes del Producto</h4>
                    <a id="btnAgregarLote" href="#" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i> Agregar Lote
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Código Lote</th>
                                <th>Cantidad Inicial</th>
                                <th>Cantidad Actual</th>
                                <th>Fecha Ingreso</th>
                                <th>Fecha Vencimiento</th>
                            </tr>
                        </thead>
                        <tbody id="lotesTableBody">
                            <tr>
                                <td colspan="5">Cargando...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./../../JS/detalle_producto.js"></script>
</body>

</html>
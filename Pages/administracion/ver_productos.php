<?php
session_start();
include __DIR__ . '/../../PHP/administracion/navbar.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado Productos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="./../../CSS/estilos_emprendedores.css">
    <link rel="stylesheet" href="./../../CSS/ver_productos.css">
</head>

<body>
    <div class="container mt-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
            <h2 class="mb-0"><i class="fas fa-box me-2"></i>Productos</h2>
            <div class="d-flex gap-2">
                <div class="btn-group flex-wrap mb-2" role="group">
                    <button id="view-cards" class="btn btn-outline-prueba btn-sm active">
                        <i class="fas fa-th-large me-1"></i> Cards
                    </button>
                    <button id="view-rows" class="btn btn-outline-pruebita btn-sm">
                        <i class="fas fa-list me-1"></i> Filas
                    </button>
                </div>
            </div>
        </div>

        <!-- FILTROS MEJORADOS -->
        <div id="filtros" class="mb-4 card filter-card">
            <div class="card-header bg-transparent py-3">
                <h5 class="mb-0"><i class="fas fa-sliders-h me-2"></i>Filtros de Productos</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <!-- Categoría -->
                    <div class="col-md-3">
                        <label for="filtro-categoria" class="form-label small text-muted mb-1">Categoría</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light"><i class="fas fa-tag"></i></span>
                            <select id="filtro-categoria" class="form-select">
                                <option value="">Todas las categorías</option>
                            </select>
                        </div>
                    </div>

                    <!-- Precio Mínimo -->
                    <div class="col-md-3">
                        <label for="filtro-precio-min" class="form-label small text-muted mb-1">Precio Mínimo</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light">$</span>
                            <input type="number" id="filtro-precio-min" class="form-control" placeholder="0" min="0">
                        </div>
                    </div>

                    <!-- Precio Máximo -->
                    <div class="col-md-3">
                        <label for="filtro-precio-max" class="form-label small text-muted mb-1">Precio Máximo</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light">$</span>
                            <input type="number" id="filtro-precio-max" class="form-control" placeholder="Sin límite" min="0">
                        </div>
                    </div>

                    <!-- Buscar por nombre -->
                    <div class="col-md-3">
                        <label for="filtro-nombre" class="form-label small text-muted mb-1">Nombre</label>
                        <input type="text" id="filtro-nombre" class="form-control" placeholder="Buscar producto...">
                    </div>




                    <!-- Disponibilidad de stock -->
                    <div class="col-md-3">
                        <label for="filtro-stock" class="form-label small text-muted mb-1">Stock</label>
                        <select id="filtro-stock" class="form-select form-select-sm">
                            <option value="">Todos</option>
                            <option value="disponible">Disponible</option>
                            <option value="bajo">Stock bajo</option>
                            <option value="agotado">Agotado</option>
                        </select>
                    </div>


                    <!-- Botones -->
                    <div class="col-md-3 d-flex align-items-end">
                        <div class="d-flex gap-2 w-100">
                            <button id="aplicar-filtros" class="btn btn-primary btn-sm flex-fill">
                                <i class="fas fa-filter me-1"></i> Aplicar
                            </button>
                            <button id="limpiar-filtros" class="btn btn-outline-secondary btn-sm" title="Limpiar filtros">
                                <i class="fas fa-eraser me-1"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Filtros avanzados (opcionales, colapsados inicialmente) -->
                <div class="mt-3 collapse " id="filtrosAvanzados">
                    <hr class="my-2">
                    <h6 class="mb-3">Filtros Avanzados</h6>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label small text-muted mb-1">Estado de stock</label>
                            <select class="form-select form-select-sm" id="filtro-stock">
                                <option value="">Todos</option>
                                <option value="disponible">Disponible</option>
                                <option value="bajo">Stock bajo</option>
                                <option value="agotado">Agotado</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small text-muted mb-1">Ordenar por</label>
                            <select class="form-select form-select-sm" id="filtro-orden">
                                <option value="nombre_asc">Nombre (A-Z)</option>
                                <option value="nombre_desc">Nombre (Z-A)</option>
                                <option value="precio_asc">Precio (Menor a Mayor)</option>
                                <option value="precio_desc">Precio (Mayor a Menor)</option>
                                <option value="recientes">Más recientes</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent py-2">
                <a class="small text-decoration-none" data-bs-toggle="collapse" href="#filtrosAvanzados" role="button">
                    <i class="fas fa-cog me-1"></i> Filtros avanzados
                </a>
            </div>
        </div>

        <!-- Spinner de carga -->
        <div id="loading-spinner" class="d-none justify-content-center my-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
        </div>

        <!-- Contador de resultados -->
        <div id="contador-resultados" class="alert alert-light border d-none mb-3 py-2">
            <span class="small text-muted" id="texto-resultados">Mostrando <span id="total-productos">0</span> productos</span>
        </div>

        <!-- Vista Cards -->
        <div id="productos-cards" class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 g-4 d-none"></div>

        <!-- Vista Filas -->
        <div id="productos-rows" class="d-none table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
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
                <tbody id="productos-rows-body"></tbody>
            </table>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./../../JS/ver_productos.js"></script>
</body>

</html>
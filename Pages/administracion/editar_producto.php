<?php
include __DIR__ . '/../../Config/auth_check.php';
include __DIR__ . '/../../PHP/administracion/navbar.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Producto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="./../../CSS/estilos_emprendedores.css">
    <link rel="stylesheet" href="./../../CSS/utilities.css">
    <link rel="stylesheet" href="./../../CSS/formularios.css">

    <style>
        /* Miniaturas de imágenes */
        .thumbs img {
            cursor: pointer;
            transition: transform 0.2s;
        }

        .thumbs img:hover {
            transform: scale(1.05);
        }

        .thumbs .position-relative {
            width: 80px;
            margin-right: 0.5rem;
        }
    </style>
</head>

<body>
    <div class="container mt-4">
        <a href="ver_productos.php" class="btn btn-secondary mb-3">
            <i class="fas fa-arrow-left me-1"></i> Volver al listado
        </a>

        <form id="form-editar-producto" enctype="multipart/form-data">
            <input type="hidden" name="id_producto" id="id_producto" value="<?= $_GET['id'] ?>">

            <div class="row g-4">
                <!-- Información del producto -->
                <div class="col-lg-6 col-12">
                    <div class="card shadow-sm h-100">
                        <div class="card-body p-4">
                            <div class="mb-3">
                                <label class="form-label">Nombre</label>
                                <input type="text" class="form-control" name="nombre" id="nombre" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Descripción</label>
                                <textarea class="form-control" name="descripcion" id="descripcion" rows="6"></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Precio de Venta</label>
                                <input type="number" class="form-control" name="precio_venta" id="precio_venta" step="0.01" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Estado</label>
                                <select class="form-select" name="estado" id="estado" required>
                                    <option value="activo">Activo</option>
                                    <option value="inactivo">Inactivo</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-success mt-2">
                                <i class="fas fa-save me-1"></i> Guardar Cambios
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Galería de imágenes -->
                <div class="col-lg-6 col-12">
                    <div class="card shadow-sm h-100">
                        <div class="card-body p-4">
                            <h5 class="mb-3">Imágenes del producto (máx. 2)</h5>
                            <div id="imagenes-container" class="d-flex flex-wrap thumbs"></div>
                            <div class="mt-3">
                                <label class="form-label">Agregar nuevas imágenes</label>
                                <input type="file" name="nuevas_imagenes[]" class="form-control" accept="image/*" multiple>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script src="./../../JS/administradores/editar_producto.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<?php
session_start();
include __DIR__ . '/../../PHP/administracion/navbar.php';
include './../../Config/conexion.php';

// Traer todas las categorías
$sql = "SELECT id_categoria, nombre
FROM categorias_productos
ORDER BY 
    CASE WHEN nombre = 'Otros' THEN 1 ELSE 0 END,  -- 'Otros' al final
    nombre ASC;                                     -- resto alfabéticamente
";
$result = $conexion->query($sql);

// Guardarlas en un array
$categorias = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $categorias[] = $row;
    }
}
?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Producto</title>
    <!-- Bootstrap primero -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="./../../CSS/estilos_emprendedores.css">
    <link rel="stylesheet" href="./../../CSS/utilities.css">
    <link rel="stylesheet" href="./../../CSS/formularios.css">
</head>

<body>
    <div class="container">
        <h1><i class="fas fa-cube" style="color: var(--color4);"></i> Agregar Nuevo Producto</h1>

        <form id="form-producto" action="./../../PHP/administracion/procesar_agregar_productos.php" method="POST" enctype="multipart/form-data">
            <div class="form-grid">
                <!-- Información básica del producto - Sección en 2 columnas -->
                <div class="form-section full-width">
                    <h2 class="section-title"><i class="fas fa-info-circle"></i> Información Básica</h2>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="prueba" for="nombre">Nombre del Producto <span class="required-asterisk">*</span></label>
                                <input type="text" id="nombre" name="nombre" required placeholder="Ej: Camiseta de algodón">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="id_categoria">Categoría <span class="required-asterisk">*</span></label>
                                <select class="form-select" id="id_categoria" name="id_categoria" required data-bs-display="static">
                                    <option value="">Seleccionar categoría</option>
                                    <?php foreach ($categorias as $categoria): ?>
                                        <option value="<?= $categoria['id_categoria'] ?>"><?= htmlspecialchars($categoria['nombre']) ?></option>
                                    <?php endforeach; ?>
                                </select>



                            </div>
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="unidad_medida_select">Unidad de Medida <span class="required-asterisk">*</span></label>

                                <!-- Select con opciones predefinidas -->
                                <select class="form-select" id="unidad_medida_select" name="unidad_medida_select" required>
                                    <option value="">Seleccionar unidad</option>
                                    <option value="kg">kg</option>
                                    <option value="litros">litros</option>
                                    <option value="unidades">unidades</option>
                                    <option value="paquete">paquete de 10</option>
                                    <option value="otro">Otro</option>
                                </select>


                                <!-- Input para unidad personalizada, oculto inicialmente -->
                                <input type="text" id="unidad_medida_custom" name="unidad_medida" class="form-control mt-2 d-none" placeholder="Ingresa tu unidad">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="descripcion">Descripción <span class="required-asterisk">*</span></label>
                                <textarea id="descripcion" name="descripcion" required placeholder="Describe detalladamente el producto..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sección de precios y stock - Organizado en filas de 2 columnas -->
                <div class="form-section full-width">
                    <h2 class="section-title"><i class="fas fa-tags"></i> Precios y Stock</h2>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="precio_venta">Precio de Venta <span class="required-asterisk">*</span></label>
                                <input type="number" id="precio_venta" name="precio_venta" min="0" step="0.01" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="costo_unitario">Costo Unitario</label>
                                <input type="number" id="costo_unitario" name="costo_unitario" min="0" step="0.01">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="codigo_lote">Código de Lote</label>
                                <input type="text" id="codigo_lote" name="codigo_lote" placeholder="Opcional (ej: LOTE-2023-001)">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="cantidad_inicial">Cantidad Inicial <span class="required-asterisk">*</span></label>
                                <input type="number" id="cantidad_inicial" name="cantidad_inicial" min="1" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="fecha_vencimiento">Fecha de Vencimiento</label>
                                <input type="date" id="fecha_vencimiento" name="fecha_vencimiento">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Imágenes del producto - Ocupa todo el ancho -->
                <!-- Imágenes del producto - Ocupa todo el ancho -->
                <div class="form-section full-width">
                    <h2 class="section-title"><i class="fas fa-images"></i> Imágenes del Producto</h2>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="images-section">
                                <div class="file-upload" onclick="document.getElementById('imagenes').click()">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <p>Haz clic para seleccionar hasta 2 imágenes</p>
                                    <p style="font-size: 0.9em; color: #666; margin-top: 10px;">Recomendado: Imágenes en alta calidad (mín. 800x800px)</p>
                                    <input type="file" id="imagenes" name="imagenes[]" multiple accept="image/*" style="display: none;">
                                </div>

                                <div class="preview-container" id="preview-container"></div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="row">
                <div class="col-md-12 text-center">
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-save"></i> Guardar Producto
                    </button>
                </div>
            </div>
        </form>
    </div>


    <script>
        document.getElementById('imagenes').addEventListener('change', function(e) {
            const previewContainer = document.getElementById('preview-container');
            previewContainer.innerHTML = '';

            // Limitar a 2 archivos
            if (this.files.length > 2) {
                alert('Solo puedes subir un máximo de 2 imágenes');
                this.value = ''; // limpiar selección
                return;
            }

            Array.from(this.files).forEach((file, index) => {
                if (file.type.match('image.*')) {
                    const reader = new FileReader();
                    const previewItem = document.createElement('div');
                    previewItem.className = 'preview-item';

                    reader.onload = function(e) {
                        previewItem.innerHTML = `
                    <img src="${e.target.result}" class="preview-image">
                    <div class="remove-image" onclick="removeImage(${index})">×</div>
                `;
                        previewContainer.appendChild(previewItem);
                    }

                    reader.readAsDataURL(file);
                }
            });
        });

        // Función para eliminar imágenes
        function removeImage(index) {
            const files = document.getElementById('imagenes').files;
            const newFiles = Array.from(files).filter((_, i) => i !== index);

            const dataTransfer = new DataTransfer();
            newFiles.forEach(file => dataTransfer.items.add(file));
            document.getElementById('imagenes').files = dataTransfer.files;

            // Volver a generar la vista previa
            document.getElementById('imagenes').dispatchEvent(new Event('change'));
        }

        // Validación del formulario
        document.getElementById('form-producto').addEventListener('submit', function(e) {
            const requiredFields = this.querySelectorAll('[required]');
            let isValid = true;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.style.borderColor = 'var(--color5)';
                    field.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                    isValid = false;
                } else {
                    field.style.borderColor = 'var(--color3)';
                }
            });

            if (!isValid) {
                e.preventDefault();
                alert('Por favor complete todos los campos requeridos');
            } else {
                const submitBtn = this.querySelector('.btn-submit');
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
                submitBtn.disabled = true;
            }
        });
    </script>
    <script>
        const select = document.getElementById('unidad_medida_select');
        const customInput = document.getElementById('unidad_medida_custom');

        // Al cambiar el select
        select.addEventListener('change', () => {
            if (select.value === 'otro') {
                // Mostrar input personalizado y quitar required del select
                customInput.classList.remove('d-none');
                customInput.required = true;
                select.required = false;
            } else {
                // Ocultar input, limpiar valor, mantener required en select
                customInput.classList.add('d-none');
                customInput.required = false;
                customInput.value = '';
                select.required = true;
            }
        });
    </script>

</body>

</html>
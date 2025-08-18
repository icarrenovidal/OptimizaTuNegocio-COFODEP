<?php
session_start();
include __DIR__ . '/../../PHP/emprendedores/navbar.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Producto</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" href="./../../CSS/estilos_emprendedores.css">
    <link rel="stylesheet" href="./../../CSS/utilities.css">
    <link rel="stylesheet" href="./../../CSS/formularios.css">
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-cube" style="color: var(--color4);"></i> Agregar Nuevo Producto</h1>
        
        <form id="form-producto" action="#" method="POST" enctype="multipart/form-data">
            <div class="form-grid">
                <!-- Información básica del producto -->
                <div class="form-section full-width">
                    <h2 class="section-title"><i class="fas fa-info-circle"></i> Información Básica</h2>
                    
                    <div class="form-group">
                        <label for="nombre">Nombre del Producto <span class="required-asterisk">*</span></label>
                        <input type="text" id="nombre" name="nombre" required placeholder="Ej: Camiseta de algodón">
                    </div>
                    
                    <div class="form-group">
                        <label for="id_categoria">Categoría <span class="required-asterisk">*</span></label>
                        <select id="id_categoria" name="id_categoria" required>
                            <option value="">Seleccionar categoría</option>
                            <?php foreach ($categorias as $categoria): ?>
                                <option value="<?= $categoria['id_categoria'] ?>"><?= htmlspecialchars($categoria['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="unidad_medida">Unidad de Medida <span class="required-asterisk">*</span></label>
                        <input type="text" id="unidad_medida" name="unidad_medida" required placeholder="Ej: kg, litros, unidades, paquete de 10">
                    </div>
                    
                    <div class="form-group full-width">
                        <label for="descripcion">Descripción <span class="required-asterisk">*</span></label>
                        <textarea id="descripcion" name="descripcion" required placeholder="Describe detalladamente el producto..."></textarea>
                    </div>
                </div>
                
                <!-- Sección de precios y stock -->
                <div class="form-section full-width">
                    <h2 class="section-title"><i class="fas fa-tags"></i> Precios y Stock</h2>
                    
                    <div class="price-section">
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="precio_venta">Precio de Venta</label>
                                <input type="number" id="precio_venta" name="precio_venta" min="0" step="0.01" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="costo_unitario">Costo Unitario</label>
                                <input type="number" id="costo_unitario" name="costo_unitario" min="0" step="0.01">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-grid" style="margin-top: 20px;">
                        <div class="form-group">
                            <label for="codigo_lote">Código de Lote</label>
                            <input type="text" id="codigo_lote" name="codigo_lote" placeholder="Opcional (ej: LOTE-2023-001)">
                        </div>
                        
                        <div class="form-group">
                            <label for="cantidad_inicial">Cantidad Inicial</label>
                            <input type="number" id="cantidad_inicial" name="cantidad_inicial" min="1" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="fecha_vencimiento">Fecha de Vencimiento</label>
                            <input type="date" id="fecha_vencimiento" name="fecha_vencimiento">
                        </div>
                    </div>
                </div>
                
                <!-- Imágenes del producto -->
                <div class="form-section full-width">
                    <h2 class="section-title"><i class="fas fa-images"></i> Imágenes del Producto</h2>
                    
                    <div class="images-section">
                        <div class="file-upload" onclick="document.getElementById('imagenes').click()">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <p>Haz clic para seleccionar imágenes o arrástralas aquí</p>
                            <p style="font-size: 0.9em; color: #666; margin-top: 10px;">Recomendado: Imágenes en alta calidad (mín. 800x800px)</p>
                            <input type="file" id="imagenes" name="imagenes[]" multiple accept="image/*" style="display: none;">
                        </div>
                        
                        <div class="preview-container" id="preview-container"></div>
                    </div>
                </div>
            </div>
            
            <button type="submit" class="btn-submit">
                <i class="fas fa-save"></i> Guardar Producto
            </button>
        </form>
    </div>

    <script>
        // Vista previa de imágenes mejorada
        document.getElementById('imagenes').addEventListener('change', function(e) {
            const previewContainer = document.getElementById('preview-container');
            previewContainer.innerHTML = '';
            
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

        // Función para eliminar imágenes de la previsualización
        function removeImage(index) {
            const files = document.getElementById('imagenes').files;
            const newFiles = Array.from(files).filter((_, i) => i !== index);
            
            // Actualizar el input file (necesita un poco más de código para esto)
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
                    field.scrollIntoView({ behavior: 'smooth', block: 'center' });
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
</body>
</html>
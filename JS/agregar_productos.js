// =========================
// Preview de imágenes
// =========================
document.getElementById('imagenes').addEventListener('change', function () {
    const previewContainer = document.getElementById('preview-container');
    previewContainer.innerHTML = '';

    if (this.files.length > 2) {
        alert('⚠️ Solo puedes subir un máximo de 2 imágenes');
        this.value = '';
        return;
    }

    Array.from(this.files).forEach((file, index) => {
        if (file.type.match('image.*')) {
            const reader = new FileReader();
            const previewItem = document.createElement('div');
            previewItem.className = 'preview-item';

            reader.onload = function (e) {
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

// =========================
// Eliminar imágenes
// =========================
function removeImage(index) {
    const files = document.getElementById('imagenes').files;
    const newFiles = Array.from(files).filter((_, i) => i !== index);

    const dataTransfer = new DataTransfer();
    newFiles.forEach(file => dataTransfer.items.add(file));
    document.getElementById('imagenes').files = dataTransfer.files;

    document.getElementById('imagenes').dispatchEvent(new Event('change'));
}

// =========================
// Unidad de medida personalizada
// =========================
const select = document.getElementById('unidad_medida_select');
const customInput = document.getElementById('unidad_medida_custom');

select.addEventListener('change', () => {
    if (select.value === 'otro') {
        customInput.classList.remove('d-none');
        customInput.required = true;
        select.required = false;
    } else {
        customInput.classList.add('d-none');
        customInput.required = false;
        customInput.value = '';
        select.required = true;
    }
});

// =========================
// Validación y envío con fetch
// =========================
document.getElementById('form-producto').addEventListener('submit', function (e) {
    e.preventDefault(); // Evita recarga de página

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
        alert('⚠️ Por favor complete todos los campos requeridos');
        return;
    }

    const form = this;
    const formData = new FormData(form);

    if (select.value === 'otro') {
        formData.set('unidad_medida', customInput.value);
    } else {
        formData.set('unidad_medida', select.value);
    }

    const submitBtn = form.querySelector('.btn-submit');
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
    submitBtn.disabled = true;

    fetch('./../../PHP/administracion/procesar_agregar_productos.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === "success") {
            alert("✅ " + data.message);
            setTimeout(() => {
                window.location.href = 'ver_productos.php';
            }, 1500);
        } else {
            alert("❌ " + data.message);
            submitBtn.innerHTML = '<i class="fas fa-save"></i> Guardar Producto';
            submitBtn.disabled = false;
        }
    })
    .catch(err => {
        console.error(err);
        alert("⚠️ Error de conexión con el servidor");
        submitBtn.innerHTML = '<i class="fas fa-save"></i> Guardar Producto';
        submitBtn.disabled = false;
    });
});

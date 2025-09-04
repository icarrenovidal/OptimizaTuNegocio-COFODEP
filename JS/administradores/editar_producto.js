const id_producto = document.getElementById('id_producto').value;
const container = document.getElementById('imagenes-container');
const eliminarImagenes = [];
const nuevasImagenesArray = [];
const MAX_IMAGENES = 2;

// Función para previsualizar nuevas imágenes
function previsualizarNuevasImagenes(files) {
    const allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

    for (let i = 0; i < files.length; i++) {
        if (container.children.length + nuevasImagenesArray.length >= MAX_IMAGENES) {
            alert(`Máximo ${MAX_IMAGENES} imágenes permitidas`);
            break;
        }

        const file = files[i];
        const ext = file.name.split('.').pop().toLowerCase();

        if (!allowed.includes(ext)) {
            alert(`Archivo no permitido: ${file.name}`);
            continue;
        }

        nuevasImagenesArray.push(file);

        const reader = new FileReader();
        reader.onload = function(e) {
            const div = document.createElement('div');
            div.className = 'position-relative d-inline-block me-2';
            div.style.width = '120px';
            div.innerHTML = `
                <img src="${e.target.result}" class="img-thumbnail w-100">
                <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0">×</button>
            `;

            // Botón eliminar para la nueva imagen
            div.querySelector('button').addEventListener('click', () => {
                const index = nuevasImagenesArray.indexOf(file);
                if (index > -1) nuevasImagenesArray.splice(index, 1);
                div.remove();
            });

            container.appendChild(div);
        };
        reader.readAsDataURL(file);
    }

    // Limpiar input para poder volver a subir las mismas imágenes si se desea
    document.querySelector('input[name="nuevas_imagenes[]"]').value = '';
}

// Cargar datos del producto
fetch(`./../../PHP/administracion/obtener_producto_para_editar.php?id=${id_producto}`)
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            document.getElementById('nombre').value = data.producto.nombre;
            document.getElementById('descripcion').value = data.producto.descripcion;
            document.getElementById('precio_venta').value = data.precio;
            document.getElementById('estado').value = data.producto.estado;

            // Mostrar imágenes existentes
            data.imagenes.forEach(img => {
                const div = document.createElement('div');
                div.className = 'position-relative d-inline-block me-2';
                div.style.width = '120px';
                div.innerHTML = `
                    <img src="./../../${img.ruta}" class="img-thumbnail w-100">
                    <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0">×</button>
                `;

                // Botón eliminar para imagen existente
                div.querySelector('button').addEventListener('click', () => {
                    eliminarImagenes.push(img.id_imagen);
                    div.remove();
                });

                container.appendChild(div);
            });
        } else {
            alert(data.message);
        }
    });

// Detectar selección de nuevas imágenes
const inputNuevas = document.querySelector('input[name="nuevas_imagenes[]"]');
inputNuevas.addEventListener('change', function() {
    previsualizarNuevasImagenes(this.files);
});

// Manejo del submit para procesar la edición
document.getElementById('form-editar-producto').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);

    // Agregar imágenes existentes eliminadas
    eliminarImagenes.forEach(id => formData.append('eliminar_imagen[]', id));

    // Agregar nuevas imágenes
    nuevasImagenesArray.forEach(file => formData.append('nuevas_imagenes[]', file));

    fetch('./../../PHP/administracion/procesar_editar_producto.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            alert('✅ ' + data.message);
            setTimeout(() => {
                window.location.href = `ver_detalle_producto.php?id=${id_producto}`;
            }, 1000);
        } else {
            alert('❌ ' + data.message);
        }
    })
    .catch(err => {
        console.error(err);
        alert('⚠️ Error de conexión con el servidor');
    });
});

document.addEventListener("DOMContentLoaded", () => {
    const productoId = new URLSearchParams(window.location.search).get("id");
    if (!productoId) return;

    const mainImage = document.getElementById("mainImage");
    const thumbsContainer = document.querySelector(".thumbs");
    const nombreProductoEl = document.getElementById("nombreProducto");
    const categoriaEl = document.getElementById("categoriaProducto");
    const precioEl = document.getElementById("precioProducto");
    const descripcionEl = document.getElementById("descripcionProducto");
    const descripcionCompletaEl = document.getElementById("descripcionCompleta");
    const btnVerMas = document.getElementById("btnVerMas");
    const stockEl = document.getElementById("stockProducto");
    const unidadEl = document.getElementById("unidadProducto");
    const lotesTableBody = document.getElementById("lotesTableBody");

    const btnEditar = document.getElementById("editarProductoBtn");
    const btnAgregarLote = document.getElementById("btnAgregarLote");

    fetch(`./../../PHP/administracion/obtener_detalle_producto.php?id=${productoId}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
                return;
            }

            const producto = data.producto;
            const lotes = data.lotes;

            // ======================
            // Actualizar información
            // ======================
            nombreProductoEl.textContent = producto.nombre_producto;
            categoriaEl.textContent = producto.nombre_categoria;
            precioEl.textContent = `$${Number(producto.precio).toLocaleString()}`;
            stockEl.textContent = producto.stock;
            unidadEl.textContent = producto.unidad_medida;

            // ======================
            // Descripción
            // ======================
            let descripcion = producto.descripcion || "";
            let corta = descripcion.length > 200 ? descripcion.substring(0, 200) + "..." : descripcion;
            descripcionEl.textContent = corta;
            descripcionCompletaEl.textContent = descripcion;

            // Mostrar botón "Ver más" solo si la descripción es larga
            if (descripcion.length > 200) {
                btnVerMas.classList.remove("d-none");
            }

            // ======================
            // Galería de imágenes
            // ======================
            thumbsContainer.innerHTML = "";
            if (producto.imagenes && producto.imagenes.length > 0) {
                mainImage.src = producto.imagenes[0];
                producto.imagenes.forEach((img, index) => {
                    const div = document.createElement("div");
                    div.classList.add("ratio", "ratio-1x1");
                    div.style.width = "80px";

                    const imgEl = document.createElement("img");
                    imgEl.src = img;
                    imgEl.classList.add("rounded", index === 0 ? "border-primary" : "border-light");
                    imgEl.style.cursor = "pointer";
                    imgEl.addEventListener("click", () => changeImage(imgEl, img));

                    div.appendChild(imgEl);
                    thumbsContainer.appendChild(div);
                });
            } else {
                mainImage.src = "https://via.placeholder.com/800?text=Sin+imagen";
            }

            // ======================
            // Tabla de lotes
            // ======================
            lotesTableBody.innerHTML = "";
            if (lotes.length > 0) {
                lotes.forEach(lote => {
                    const tr = document.createElement("tr");
                    tr.innerHTML = `
                        <td>${lote.codigo_lote}</td>
                        <td>${lote.cantidad_inicial}</td>
                        <td>${lote.cantidad_actual}</td>
                        <td>${new Date(lote.fecha_ingreso).toLocaleDateString()}</td>
                        <td>${lote.fecha_vencimiento ? new Date(lote.fecha_vencimiento).toLocaleDateString() : '-'}</td>
                    `;
                    lotesTableBody.appendChild(tr);
                });
            } else {
                const tr = document.createElement("tr");
                tr.innerHTML = `<td colspan="5" class="text-muted">No hay lotes registrados para este producto.</td>`;
                lotesTableBody.appendChild(tr);
            }

            // ======================
            // Botones Editar y Agregar Lote
            // ======================
            if (btnEditar) btnEditar.href = `editar_producto.php?id=${producto.id_producto}`;
            if (btnAgregarLote) btnAgregarLote.href = `agregar_stock.php?id=${producto.id_producto}`;

        })
        .catch(err => {
            console.error("Error cargando el producto:", err);
            alert("No se pudo cargar la información del producto.");
        });
});

// Función para cambiar imagen principal
function changeImage(thumb, imgSrc) {
    document.getElementById('mainImage').src = imgSrc;
    document.querySelectorAll('.thumbs img').forEach(img => {
        img.classList.remove('border-primary');
        img.classList.add('border-light');
    });
    thumb.classList.remove('border-light');
    thumb.classList.add('border-primary');
}

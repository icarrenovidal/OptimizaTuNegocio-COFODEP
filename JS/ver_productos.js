document.addEventListener("DOMContentLoaded", () => {
  // Vistas y elementos principales
  const btnCards = document.getElementById("view-cards");
  const btnRows = document.getElementById("view-rows");
  const viewCards = document.getElementById("productos-cards");
  const viewRows = document.getElementById("productos-rows");
  const rowsBody = document.getElementById("productos-rows-body");
  const spinner = document.getElementById("loading-spinner");

  // Filtros
  const selCategoria = document.getElementById("filtro-categoria");
  const inpPrecioMin = document.getElementById("filtro-precio-min");
  const inpPrecioMax = document.getElementById("filtro-precio-max");
  const inpNombre = document.getElementById("filtro-nombre");
  const selStock = document.getElementById("filtro-stock");
  const btnFiltrar = document.getElementById("aplicar-filtros");
  const btnLimpiar = document.getElementById("limpiar-filtros");

  // Contador de resultados
  const contadorWrap = document.getElementById("contador-resultados");
  const totalEl = document.getElementById("total-productos");

  // --- Funciones de renderizado ---
  function renderCards(productos) {
    viewCards.innerHTML = "";
    productos.forEach((prod) => {
      const card = `
        <div class="col">
          <div class="card h-100 shadow-sm position-relative">
            <div class="img-container position-relative">
              <!-- Botón flotante sobre la imagen -->
              

              <img src="${
                prod.imagenes[0] ||
                "https://via.placeholder.com/300?text=Sin+imagen"
              }"
                class="primary-img" alt="${prod.nombre_producto}">
              <img src="${
                prod.imagenes[1] ||
                prod.imagenes[0] ||
                "https://via.placeholder.com/300?text=Sin+imagen"
              }"
                class="hover-img" alt="${
                  prod.nombre_producto
                } (vista alternativa)">
            </div>

            <div class="card-body">
              <h5 class="card-title mb-1">${prod.nombre_producto}</h5>
              <span class="price-tag d-block">$${Number(
                prod.precio
              ).toLocaleString()}</span>
              <span class="badge bg-secondary mb-2">${
                prod.nombre_categoria
              }</span>
              <p class="card-text">${prod.descripcion || ""}</p>
            </div>

            <div class="card-footer bg-white border-top-0 pt-0">
              <div class="d-flex justify-content-between align-items-center flex-wrap w-100">
                <small class="text-muted">Disponibles: ${prod.stock} ${
        prod.unidad_medida
      }</small>
                <div class="d-flex gap-2 mt-1 mt-md-0">
                  <a href="ver_detalle_producto.php?id=${
                    prod.id_producto
                  }" class="btn btn-sm btn-outline-prueba">
                    <i class="fas fa-eye me-1"></i> Detalle
                  </a>
                  <a href="#" class="btn btn-sm btn-outline-success">
                    <i class="fas fa-cart-plus me-1"></i> Agregar 
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>`;
      viewCards.insertAdjacentHTML("beforeend", card);
    });
  }

  function renderRows(productos) {
    rowsBody.innerHTML = "";
    const maxChars = 50;
    productos.forEach((prod) => {
      let descripcionCorta = prod.descripcion || "Sin descripción";
      if (descripcionCorta.length > maxChars)
        descripcionCorta = descripcionCorta.substring(0, maxChars) + "...";
      const row = `
        <tr>
            <td data-label="Imagen">
                <img src="${
                  prod.imagenes[0] ||
                  "https://via.placeholder.com/50?text=Sin+imagen"
                }"
                class="img-table"
                alt="${prod.nombre_producto}">
            </td>
            <td data-label="Producto">${prod.nombre_producto}</td>
            <td data-label="Categoría">${prod.nombre_categoria}</td>
            <td data-label="Precio">$${Number(
              prod.precio
            ).toLocaleString()}</td>
            <td data-label="Stock">${prod.stock} ${prod.unidad_medida}</td>
            <td data-label="Descripción" class="small descripcion" title="${
              prod.descripcion || ""
            }">
                ${descripcionCorta}
            </td>
            <td data-label="Acciones" class="actions-cell">
                <a href="ver_detalle_producto.php?id=${
                  prod.id_producto
                }" class="btn btn-sm btn-outline-prueba" title="Ver detalle">
                    <i class="fas fa-eye"></i>
                </a>
                <a href="#" class="btn btn-sm btn-success btn-add-cart" data-id="${
                  prod.id_producto
                }" title="Agregar al carrito">
                    <i class="fas fa-cart-plus"></i>
                </a>
            </td>
        </tr>`;
      rowsBody.insertAdjacentHTML("beforeend", row);
    });
  }

  function updateCounter(n) {
    if (!contadorWrap || !totalEl) return;
    totalEl.textContent = n;
    contadorWrap.classList.remove("d-none");
  }

  function showEmptyState() {
    viewCards.innerHTML = `
        <div class="col">
          <div class="alert alert-light border text-center">No se encontraron productos con esos filtros.</div>
        </div>`;
    rowsBody.innerHTML = `
        <tr>
          <td colspan="7" class="text-center">
            <div class="alert alert-light border mb-0">No se encontraron productos con esos filtros.</div>
          </td>
        </tr>`;
  }

  // --- Función de carga de productos ---
  function loadProductos(paramsObj = {}) {
    spinner.classList.remove("d-none");

    const params = new URLSearchParams();
    if (paramsObj.categoria) params.append("categoria", paramsObj.categoria);
    if (paramsObj.precio_min) params.append("precio_min", paramsObj.precio_min);
    if (paramsObj.precio_max) params.append("precio_max", paramsObj.precio_max);
    if (paramsObj.nombre) params.append("nombre", paramsObj.nombre);
    if (paramsObj.stock) params.append("stock", paramsObj.stock);

    const url = params.toString()
      ? `./../../PHP/administracion/obtener_productos.php?${params.toString()}`
      : `./../../PHP/administracion/obtener_productos.php`;

    fetch(url)
      .then((res) => res.json())
      .then((data) => {
        updateCounter(Array.isArray(data) ? data.length : 0);
        if (!Array.isArray(data) || data.length === 0) {
          showEmptyState();
        } else {
          renderCards(data);
          renderRows(data);
        }
      })
      .catch((err) => {
        console.error("Error cargando productos:", err);
        viewCards.innerHTML = `<div class="col"><div class="alert alert-danger">Error al cargar productos</div></div>`;
        rowsBody.innerHTML = `<tr><td colspan="7" class="text-center"><div class="alert alert-danger mb-0">Error al cargar productos</div></td></tr>`;
      })
      .finally(() => spinner.classList.add("d-none"));
  }

  // --- Inicializar vistas ---
  viewCards.classList.remove("d-none");
  viewRows.classList.add("d-none");
  btnCards.classList.add("active");
  btnRows.classList.remove("active");

  loadProductos(); // Carga inicial

  // --- Cambiar vistas ---
  btnCards.addEventListener("click", () => {
    viewCards.classList.remove("d-none");
    viewRows.classList.add("d-none");
    btnCards.classList.add("active");
    btnRows.classList.remove("active");
  });
  btnRows.addEventListener("click", () => {
    viewRows.classList.remove("d-none");
    viewCards.classList.add("d-none");
    btnRows.classList.add("active");
    btnCards.classList.remove("active");
  });

  // --- Cargar categorías ---
  fetch("./../../PHP/administracion/obtener_categorias.php")
    .then((res) => res.json())
    .then((data) => {
      data.forEach((cat) => {
        const option = document.createElement("option");
        option.value = cat.id_categoria;
        option.textContent = cat.nombre;
        selCategoria.appendChild(option);
      });
    })
    .catch((err) => console.error("Error cargando categorías:", err));

  // --- Filtros y búsqueda automática ---
  function aplicarFiltros() {
    loadProductos({
      categoria: selCategoria?.value || "",
      precio_min: inpPrecioMin?.value || "",
      precio_max: inpPrecioMax?.value || "",
      nombre: inpNombre?.value.trim() || "",
      stock: selStock?.value || "",
    });
  }

  btnFiltrar.addEventListener("click", aplicarFiltros);

  [inpPrecioMin, inpPrecioMax, inpNombre, selStock, selCategoria].forEach(
    (el) => {
      if (!el) return;
      el.addEventListener("input", aplicarFiltros); // Búsqueda en tiempo real
    }
  );

  // Limpiar filtros
  btnLimpiar.addEventListener("click", () => {
    selCategoria.value = "";
    inpPrecioMin.value = "";
    inpPrecioMax.value = "";
    inpNombre.value = "";
    selStock.value = "";
    aplicarFiltros();
  });
});

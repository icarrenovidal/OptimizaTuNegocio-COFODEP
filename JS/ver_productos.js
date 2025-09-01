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

  // Paginación
  const paginationWrap = document.getElementById("pagination-wrap"); // contenedor para botones
  let currentPage = 1;
  const limit = 10; // productos por página, se puede cambiar

  // --- Funciones de renderizado ---
  function renderCards(productos) {
    viewCards.innerHTML = "";
    productos.forEach((prod) => {
      const isAgotado = prod.stock <= 0;
      const isInactivo = prod.estado === "inactivo";

      const card = `
      <div class="col">
        <div class="card h-100 shadow-sm position-relative ${isInactivo ? 'opacity-50' : ''}">
          <div class="img-container position-relative">
            <img src="${prod.imagenes[0] || "https://via.placeholder.com/300?text=Sin+imagen"}" 
                 class="primary-img" alt="${prod.nombre_producto}">
            <img src="${prod.imagenes[1] || prod.imagenes[0] || "https://via.placeholder.com/300?text=Sin+imagen"}" 
                 class="hover-img" alt="${prod.nombre_producto} (vista alternativa)">
          </div>

          <div class="card-body">
            <h5 class="card-title mb-1">${prod.nombre_producto}</h5>
            <span class="price-tag d-block">$${Number(prod.precio).toLocaleString()}</span>
            <span class="badge bg-secondary mb-2">${prod.nombre_categoria}</span>
            <p class="card-text">${prod.descripcion || ""}</p>
          </div>

          <div class="card-footer bg-white border-top-0 pt-0">
            <div class="d-flex justify-content-between align-items-center flex-wrap w-100">
              <small class="text-muted">Disponibles: ${prod.stock} ${prod.unidad_medida}</small>
              <div class="d-flex gap-2 mt-1 mt-md-0">
                <a href="ver_detalle_producto.php?id=${prod.id_producto}" class="btn btn-sm btn-outline-prueba">
                  <i class="fas fa-eye me-1"></i> Detalle
                </a>
                <a href="#" class="btn btn-sm btn-${isAgotado || isInactivo ? "secondary" : "outline-success"} btn-add-cart" 
                   data-id="${prod.id_producto}" 
                   ${isAgotado || isInactivo ? "aria-disabled='true' style='pointer-events:none;opacity:0.65;'" : ""}>
                  <i class="fas fa-cart-plus me-1"></i> ${isAgotado ? "Sin stock" : isInactivo ? "Inactivo" : "Agregar"}
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
      if (descripcionCorta.length > maxChars) descripcionCorta = descripcionCorta.substring(0, maxChars) + "...";

      const isAgotado = prod.stock <= 0;
      const isInactivo = prod.estado === "inactivo";

      const row = `
      <tr class="${isInactivo ? 'opacity-50' : ''}">
          <td data-label="Imagen">
              <img src="${prod.imagenes[0] || "https://via.placeholder.com/50?text=Sin+imagen"}" 
                   class="img-table" alt="${prod.nombre_producto}">
          </td>
          <td data-label="Producto">${prod.nombre_producto}</td>
          <td data-label="Categoría">${prod.nombre_categoria}</td>
          <td data-label="Precio">$${Number(prod.precio).toLocaleString()}</td>
          <td data-label="Stock">${prod.stock} ${prod.unidad_medida}</td>
          <td data-label="Descripción" class="small descripcion" title="${prod.descripcion || ""}">
              ${descripcionCorta}
          </td>
          <td data-label="Acciones" class="actions-cell">
              <a href="ver_detalle_producto.php?id=${prod.id_producto}" class="btn btn-sm btn-outline-prueba" title="Ver detalle">
                  <i class="fas fa-eye"></i>
              </a>
              <a href="#" class="btn btn-sm btn-${isAgotado || isInactivo ? "secondary" : "success"} btn-add-cart" 
                 data-id="${prod.id_producto}" 
                 title="${isAgotado ? "Sin stock" : isInactivo ? "Producto inactivo" : "Agregar al carrito"}"
                 ${isAgotado || isInactivo ? "aria-disabled='true' style='pointer-events:none;opacity:0.65;'" : ""}>
                  <i class="fas fa-${isAgotado ? "ban" : isInactivo ? "ban" : "cart-plus"}"></i>
              </a>
          </td>
      </tr>`;
      rowsBody.insertAdjacentHTML("beforeend", row);
    });
  }

  function updateCounter(total) {
    if (!contadorWrap || !totalEl) return;
    totalEl.textContent = total;
    contadorWrap.classList.remove("d-none");
  }

  function showEmptyState() {
    viewCards.innerHTML = `<div class="col"><div class="alert alert-light border text-center">No se encontraron productos con esos filtros.</div></div>`;
    rowsBody.innerHTML = `<tr><td colspan="7" class="text-center"><div class="alert alert-light border mb-0">No se encontraron productos con esos filtros.</div></td></tr>`;
    if(paginationWrap) paginationWrap.innerHTML = ""; // limpiar paginación si no hay productos
  }

  // --- Función para renderizar la paginación ---
  function renderPagination(total) {
    if(!paginationWrap) return;
    const totalPages = Math.ceil(total / limit);
    paginationWrap.innerHTML = "";

    if(totalPages <= 1) return; // no mostrar si solo hay 1 página

    for(let i=1; i<=totalPages; i++){
      const btn = document.createElement("button");
      btn.textContent = i;
      btn.className = `btn btn-sm me-1 ${i === currentPage ? "btn-primary" : "btn-outline-primary"}`;
      btn.addEventListener("click", () => {
        if(i === currentPage) return;
        currentPage = i;
        aplicarFiltros(false); // NO resetear página
      });
      paginationWrap.appendChild(btn);
    }
  }

  // --- Función de carga de productos ---
  function loadProductos(paramsObj = {}) {
    spinner.classList.remove("d-none");

    const params = new URLSearchParams();
    if(paramsObj.categoria) params.append("categoria", paramsObj.categoria);
    if(paramsObj.precio_min) params.append("precio_min", paramsObj.precio_min);
    if(paramsObj.precio_max) params.append("precio_max", paramsObj.precio_max);
    if(paramsObj.nombre) params.append("nombre", paramsObj.nombre);
    if(paramsObj.stock) params.append("stock", paramsObj.stock);

    // agregar paginación
    params.append("page", currentPage);
    params.append("limit", limit);

    const url = `./../../PHP/administracion/obtener_productos.php?${params.toString()}`;

    fetch(url)
      .then(res => res.json())
      .then(data => {
        if(!data.productos || data.productos.length === 0){
          showEmptyState();
          updateCounter(0);
        } else {
          renderCards(data.productos);
          renderRows(data.productos);
          updateCounter(data.total);
          renderPagination(data.total);
        }
      })
      .catch(err => {
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

  loadProductos(); // carga inicial

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
    .then(res => res.json())
    .then(data => {
      data.forEach(cat => {
        const option = document.createElement("option");
        option.value = cat.id_categoria;
        option.textContent = cat.nombre;
        selCategoria.appendChild(option);
      });
    })
    .catch(err => console.error("Error cargando categorías:", err));

  // --- Filtros y búsqueda automática ---
  function aplicarFiltros(resetPage = true){
    if(resetPage) currentPage = 1; // solo resetear cuando es desde filtros
    loadProductos({
      categoria: selCategoria?.value || "",
      precio_min: inpPrecioMin?.value || "",
      precio_max: inpPrecioMax?.value || "",
      nombre: inpNombre?.value.trim() || "",
      stock: selStock?.value || "",
    });
  }

  btnFiltrar.addEventListener("click", () => aplicarFiltros());

  [inpPrecioMin, inpPrecioMax, inpNombre, selStock, selCategoria].forEach(el => {
    if(!el) return;
    el.addEventListener("input", () => aplicarFiltros());
  });

  // Limpiar filtros
  btnLimpiar.addEventListener("click", () => {
    selCategoria.value = "";
    inpPrecioMin.value = "";
    inpPrecioMax.value = "";
    inpNombre.value = "";
    selStock.value = "";
    aplicarFiltros();
  });

  // AGREGAR PRODUCTOS AL CARRO
  document.addEventListener("click", function(e){
    if(e.target.closest(".btn-add-cart")){
      e.preventDefault();
      const btn = e.target.closest(".btn-add-cart");
      const id_producto = btn.dataset.id;

      fetch("/OptimizaTuNegocio/OptimizaTuNegocio/Pages/administracion/carrito_actions.php", {
        method: "POST",
        headers: {"Content-Type": "application/x-www-form-urlencoded"},
        body: `action=add&id_producto=${id_producto}&cantidad=1`
      })
      .then(res => res.json())
      .then(data => {
        if(data.status === "ok"){
          actualizarContadorCarrito(data.total_carrito);
          alert("Producto agregado al carrito!");
        } else {
          alert("Error al agregar producto");
        }
      });
    }
  });
});

// --- Función para actualizar contador del navbar ---
function actualizarContadorCarrito(total){
  const contador = document.querySelector(".cart-count");
  if(!contador) return;
  contador.textContent = total;
}

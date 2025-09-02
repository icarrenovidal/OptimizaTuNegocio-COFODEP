document.addEventListener("DOMContentLoaded", () => {
  const bodyVentas = document.getElementById("ventas-body");
  const fechaDesde = document.getElementById("filtro-fecha-desde");
  const fechaHasta = document.getElementById("filtro-fecha-hasta");
  const canalFiltro = document.getElementById("filtro-canal");
  const metodoFiltro = document.getElementById("filtro-metodo");
  const pageInfo = document.getElementById("page-info");
  const paginationWrap = document.getElementById("pagination-wrap");

  let currentPage = 1;
  const perPage = 15;
  let totalPages = 1;

  const today = new Date().toISOString().split("T")[0];
  fechaDesde.value = today;
  fechaHasta.value = today;

  function loadVentas() {
    bodyVentas.innerHTML = `<tr><td colspan="6" class="text-center">Cargando ventas...</td></tr>`;

    const params = new URLSearchParams();
    if (fechaDesde.value) params.append("fecha_inicio", fechaDesde.value);
    if (fechaHasta.value) params.append("fecha_fin", fechaHasta.value);
    if (canalFiltro.value) params.append("canal", canalFiltro.value);
    if (metodoFiltro.value) params.append("metodo_pago", metodoFiltro.value);
    params.append("page", currentPage);
    params.append("per_page", perPage);

    fetch(`./../../PHP/administracion/obtener_ventas.php?${params.toString()}`)
      .then(res => res.json())
      .then(data => {
        if (!data || !Array.isArray(data.ventas) || data.ventas.length === 0) {
          bodyVentas.innerHTML = `<tr><td colspan="6" class="text-center">No se encontraron ventas</td></tr>`;
          pageInfo.textContent = "";
          paginationWrap.innerHTML = "";
          return;
        }

        bodyVentas.innerHTML = "";
        data.ventas.forEach(v => {
          const row = `
<tr>
  <td data-label="ID Venta">${v.id_venta}</td>
  <td data-label="Fecha">${v.fecha}</td>
  <td data-label="Total">$${Number(v.total).toLocaleString()}</td>
  <td data-label="Canal">${v.canal_venta}</td>
  <td data-label="Método de Pago">${v.metodo_pago || ""}</td>
  <td data-label="Acciones">
    <button class="btn btn-outline-prueba btn-sm btn-detalle" data-id="${v.id_venta}">
      <i class="fas fa-eye"></i> Ver detalle
    </button>
  </td>
</tr>`;
          bodyVentas.insertAdjacentHTML("beforeend", row);
        });

        totalPages = Math.ceil(data.total / perPage);
        pageInfo.textContent = `Página ${currentPage} de ${totalPages}`;
        renderPagination(data.total);
      })
      .catch(err => {
        bodyVentas.innerHTML = `<tr><td colspan="6" class="text-center text-danger">Error cargando ventas</td></tr>`;
        console.error(err);
      });
  }

  function renderPagination(total) {
    if (!paginationWrap) return;
    paginationWrap.innerHTML = "";
    totalPages = Math.ceil(total / perPage);
    if (totalPages <= 1) return;

    const maxButtons = 5;
    let startPage = Math.max(1, currentPage - Math.floor(maxButtons / 2));
    let endPage = startPage + maxButtons - 1;
    if (endPage > totalPages) {
      endPage = totalPages;
      startPage = Math.max(1, endPage - maxButtons + 1);
    }

    const createBtn = (text, page, disabled = false, active = false) => {
      const btn = document.createElement("button");
      btn.textContent = text;
      btn.className = `btn btn-sm me-1 ${active ? "btn-primary" : disabled ? "btn-secondary" : "btn-outline-primary"}`;
      btn.disabled = disabled;
      btn.addEventListener("click", () => {
        if (page === currentPage) return;
        currentPage = page;
        loadVentas();
      });
      return btn;
    };

    // Botones << < 1 2 3 > >>
    paginationWrap.appendChild(createBtn("<<", 1, currentPage === 1));
    paginationWrap.appendChild(createBtn("<", Math.max(1, currentPage - 1), currentPage === 1));

    for (let i = startPage; i <= endPage; i++) {
      paginationWrap.appendChild(createBtn(i, i, false, i === currentPage));
    }

    paginationWrap.appendChild(createBtn(">", Math.min(totalPages, currentPage + 1), currentPage === totalPages));
    paginationWrap.appendChild(createBtn(">>", totalPages, currentPage === totalPages));
  }

  [fechaDesde, fechaHasta, canalFiltro, metodoFiltro].forEach(el => {
    el.addEventListener("change", () => {
      currentPage = 1;
      loadVentas();
    });
  });

  document.addEventListener("click", e => {
    if (e.target.closest(".btn-detalle")) {
      const idVenta = e.target.closest(".btn-detalle").dataset.id;
      window.location.href = `detalle_venta.php?id=${idVenta}`;
    }
  });

  loadVentas();
});

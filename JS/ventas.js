document.addEventListener("DOMContentLoaded", () => {
  const bodyVentas = document.getElementById("ventas-body");
  const fechaDesde = document.getElementById("filtro-fecha-desde");
  const fechaHasta = document.getElementById("filtro-fecha-hasta");
  const canalFiltro = document.getElementById("filtro-canal");
  const metodoFiltro = document.getElementById("filtro-metodo");
  const btnPrev = document.getElementById("prev-page");
  const btnNext = document.getElementById("next-page");
  const pageInfo = document.getElementById("page-info");

  let currentPage = 1;
  const perPage = 15;
  let totalPages = 1;

  // Fecha por defecto: hoy
  const today = new Date().toISOString().split("T")[0];
  fechaDesde.value = today;
  fechaHasta.value = today;

  function loadVentas() {
    bodyVentas.innerHTML = `<tr><td colspan="5" class="text-center">Cargando ventas...</td></tr>`;

    const params = new URLSearchParams();
    if (fechaDesde.value) params.append("fecha_inicio", fechaDesde.value);
    if (fechaHasta.value) params.append("fecha_fin", fechaHasta.value);
    if (canalFiltro.value) params.append("canal", canalFiltro.value);
    if (metodoFiltro.value) params.append("metodo_pago", metodoFiltro.value);
    params.append("page", currentPage);
    params.append("per_page", perPage);

    fetch(`./../../PHP/administracion/obtener_ventas.php?${params.toString()}`)
      .then((res) => res.json())
      .then((data) => {
        if (!data || !Array.isArray(data.ventas) || data.ventas.length === 0) {
          bodyVentas.innerHTML = `<tr><td colspan="5" class="text-center">No se encontraron ventas</td></tr>`;
          pageInfo.textContent = "";
          totalPages = 1;
          btnPrev.disabled = true;
          btnNext.disabled = true;
          return;
        }

        bodyVentas.innerHTML = "";
        data.ventas.forEach((v) => {
          const row = `
<tr>
    <td data-label="ID Venta">${v.id_venta}</td>
    <td data-label="Fecha">${v.fecha}</td>
    <td data-label="Total">$${Number(v.total).toLocaleString()}</td>
    <td data-label="Canal">${v.canal_venta}</td>
    <td data-label="Método de Pago">${v.metodo_pago}</td> <!-- Nueva columna -->
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
        btnPrev.disabled = currentPage <= 1;
        btnNext.disabled = currentPage >= totalPages;
      })
      .catch((err) => {
        bodyVentas.innerHTML = `<tr><td colspan="5" class="text-center text-danger">Error cargando ventas</td></tr>`;
        console.error(err);
      });
  }

  // Ejecutar al cambiar cualquier filtro
  [fechaDesde, fechaHasta, canalFiltro, metodoFiltro].forEach((el) => {
    el.addEventListener("change", () => {
      currentPage = 1;
      loadVentas();
    });
  });

  // Botones de paginación
  btnPrev?.addEventListener("click", () => {
    if (currentPage > 1) {
      currentPage--;
      loadVentas();
    }
  });

  btnNext?.addEventListener("click", () => {
    if (currentPage < totalPages) {
      currentPage++;
      loadVentas();
    }
  });

  // Carga inicial
  loadVentas();

  // Detalle de venta
  document.addEventListener("click", (e) => {
    if (e.target.closest(".btn-detalle")) {
      const idVenta = e.target.closest(".btn-detalle").dataset.id;
      window.location.href = `detalle_venta.php?id=${idVenta}`;
    }
  });
});

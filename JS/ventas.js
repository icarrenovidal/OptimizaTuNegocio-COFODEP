document.addEventListener("DOMContentLoaded", () => {
  const bodyVentas = document.getElementById("ventas-body");
  const fechaDesde = document.getElementById("filtro-fecha-desde");
  const fechaHasta = document.getElementById("filtro-fecha-hasta");
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

  function loadVentas(fecha_inicio = "", fecha_fin = "", page = 1) {
    bodyVentas.innerHTML = `<tr><td colspan="5" class="text-center">Cargando ventas...</td></tr>`;

    const params = new URLSearchParams();
    if (fecha_inicio) params.append("fecha_inicio", fecha_inicio);
    if (fecha_fin) params.append("fecha_fin", fecha_fin);
    params.append("page", page);
    params.append("per_page", perPage);

    fetch(`./../../PHP/administracion/obtener_ventas.php?${params.toString()}`)
      .then((res) => res.json())
      .then((data) => {
        if (!data || !Array.isArray(data.ventas) || data.ventas.length === 0) {
          bodyVentas.innerHTML = `<tr><td colspan="5" class="text-center">No se encontraron ventas</td></tr>`;
          pageInfo.textContent = "";
          totalPages = 1;
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
    <td data-label="Acciones">
        <button class="btn btn-outline-prueba btn-sm btn-detalle" data-id="${
          v.id_venta
        }">
            <i class="fas fa-eye"></i> Ver detalle
        </button>
    </td>
</tr>`;

          bodyVentas.insertAdjacentHTML("beforeend", row);
        });

        // Paginación
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

  // Ejecutar automáticamente al cambiar fechas
  [fechaDesde, fechaHasta].forEach((input) => {
    input.addEventListener("change", () => {
      currentPage = 1;
      loadVentas(fechaDesde.value, fechaHasta.value, currentPage);
    });
  });

  // Botones de paginación
  btnPrev?.addEventListener("click", () => {
    if (currentPage > 1) {
      currentPage--;
      loadVentas(fechaDesde.value, fechaHasta.value, currentPage);
    }
  });

  btnNext?.addEventListener("click", () => {
    if (currentPage < totalPages) {
      currentPage++;
      loadVentas(fechaDesde.value, fechaHasta.value, currentPage);
    }
  });

  // Carga inicial
  loadVentas(today, today, currentPage);

  // Detalle de venta
  document.addEventListener("click", (e) => {
    if (e.target.closest(".btn-detalle")) {
      const idVenta = e.target.closest(".btn-detalle").dataset.id;
      window.location.href = `detalle_venta.php?id=${idVenta}`;
    }
  });
});

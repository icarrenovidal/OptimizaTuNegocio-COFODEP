document.addEventListener("DOMContentLoaded", () => {
  const detalleBody = document.getElementById("detalle-body");
  const ventaIdEl = document.getElementById("venta-id");
  const ventaFechaEl = document.getElementById("venta-fecha");
  const ventaCanalEl = document.getElementById("venta-canal");
  const ventaTotalEl = document.getElementById("venta-total");

  // Obtener id de venta de la URL
  const params = new URLSearchParams(window.location.search);
  const idVenta = params.get("id");

  if (!idVenta) {
    detalleBody.innerHTML = `<tr><td colspan="4" class="text-center text-danger">ID de venta no proporcionado</td></tr>`;
    return;
  }

  fetch(`./../../PHP/administracion/obtener_detalle_venta.php?id=${idVenta}`)
    .then((res) => res.json())
    .then((data) => {
      if (!data.venta) {
        detalleBody.innerHTML = `<tr><td colspan="4" class="text-center text-danger">Venta no encontrada</td></tr>`;
        return;
      }

      // Datos generales

      ventaFechaEl.textContent = data.venta.fecha;
      ventaCanalEl.textContent = data.venta.canal_venta;
      ventaTotalEl.textContent = `$${Number(
        data.venta.total
      ).toLocaleString()}`;

      // Detalle de productos
      detalleBody.innerHTML = "";
      data.detalle.forEach((item) => {
        const row = `
                    <tr>
                       <td data-label="Producto">${item.producto}</td>
      <td data-label="Cantidad">${item.cantidad}</td>
      <td data-label="Precio Unitario">$${Number(
        item.precio_unitario
      ).toLocaleString()}</td>
      <td data-label="Subtotal">$${Number(item.subtotal).toLocaleString()}</td>
                    </tr>`;
        detalleBody.insertAdjacentHTML("beforeend", row);
      });
    })
    .catch((err) => {
      detalleBody.innerHTML = `<tr><td colspan="4" class="text-center text-danger">Error cargando detalle</td></tr>`;
      console.error(err);
    });
});

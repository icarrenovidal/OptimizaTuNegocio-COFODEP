document.addEventListener("DOMContentLoaded", () => {
  const productoId = new URLSearchParams(window.location.search).get("id");
  if (!productoId) return;

  const nombreProductoEl = document.getElementById("nombreProducto");
  const loteSelect = document.getElementById("id_lote");
  const form = document.getElementById("formDescontarProducto");

  // ======================
  // Cargar datos del producto
  // ======================
  fetch(`./../../PHP/administracion/obtener_detalle_producto.php?id=${productoId}`)
    .then(res => res.json())
    .then(data => {
      if (data.error) {
        alert(data.error);
        return;
      }

      const producto = data.producto;
      const lotes = data.lotes;

      // Mostrar nombre
      nombreProductoEl.value = producto.nombre_producto;

      // Poblar lotes con stock > 0
      loteSelect.innerHTML = `<option value="">-- Seleccione un lote --</option>`;
      const lotesConStock = lotes.filter(lote => lote.cantidad_actual > 0);

      if (lotesConStock.length > 0) {
        lotesConStock.forEach(lote => {
          const option = document.createElement("option");
          option.value = lote.id_lote;
          option.textContent = `${lote.codigo_lote} - Stock: ${lote.cantidad_actual}`;
          loteSelect.appendChild(option);
        });
      } else {
        const option = document.createElement("option");
        option.value = "";
        option.textContent = "Sin lotes con stock disponible";
        loteSelect.appendChild(option);
      }
    })
    .catch(err => {
      console.error("Error cargando producto:", err);
      alert("No se pudo cargar la información del producto.");
    });

  // ======================
  // Manejar envío del formulario
  // ======================
  form.addEventListener("submit", (e) => {
    e.preventDefault();

    const formData = new FormData(form);

    fetch("./../../PHP/administracion/descontar_producto_logic.php", {
      method: "POST",
      body: formData
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        alert("✅ Descuento realizado correctamente.");
        // Redirigir a ver_productos.php
        window.location.href = `ver_productos.php`;
      } else {
        alert("❌ Error: " + (data.error || "No se pudo realizar el descuento."));
      }
    })
    .catch(err => {
      console.error("Error en la solicitud:", err);
      alert("Ocurrió un error al enviar los datos.");
    });
  });
});

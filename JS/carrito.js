document.addEventListener("DOMContentLoaded", () => {
  const cartBody = document.getElementById("cart-body");
  const cartCount = document.getElementById("cart-count");

  // --- Cargar carrito desde la sesión ---
  function loadCart() {
    fetch(
      "/OptimizaTuNegocio/OptimizaTuNegocio/Pages/administracion/carrito_actions.php",
      {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "action=list",
      }
    )
      .then((res) => res.json())
      .then((data) => {
        cartBody.innerHTML = "";
        if (!data || data.length === 0) {
          cartCount.textContent = "0 productos";
          checkEmptyCart();
          updateCartSummary();
          return;
        }
        data.forEach((prod) => addCartRow(prod));
        checkEmptyCart();
        updateCartSummary();
      })
      .catch((err) => console.error("Error cargando carrito:", err));
  }

  // --- Agregar fila al carrito ---
  function addCartRow(prod) {
    const row = document.createElement("tr");
    row.dataset.id = prod.id_producto;
    row.innerHTML = `
            <td>
                <div class="d-flex align-items-center">
                    <img src="${prod.imagen}" class="cart-item-img me-3" alt="${
      prod.nombre_producto
    }">
                    <div>
                        <h6 class="cart-item-title mb-1">${
                          prod.nombre_producto
                        }</h6>
                        <small class="cart-item-code">Código: ${
                          prod.id_producto
                        }</small>
                    </div>
                </div>
            </td>
            <td class="item-price fw-bold text-prueba">${formatCurrency(
              prod.precio
            )}</td>
            <td>
                <div class="input-group quantity-control">
                    <button class="btn quantity-btn btn-sm" type="button">-</button>
                    <input type="number" class="form-control form-control-sm quantity-input" value="${
                      prod.cantidad
                    }" min="1">
                    <button class="btn quantity-btn btn-sm" type="button">+</button>
                </div>
            </td>
            <td class="item-total fw-bold text-prueba">${formatCurrency(
              prod.precio * prod.cantidad
            )}</td>
            <td class="item-actions">
                <button class="btn btn-sm btn-outline-danger" type="button">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
    cartBody.appendChild(row);

    const minusBtn = row.querySelector(".quantity-btn:first-child");
    const plusBtn = row.querySelector(".quantity-btn:last-child");
    const qtyInput = row.querySelector(".quantity-input");
    const removeBtn = row.querySelector(".item-actions button");

    minusBtn.addEventListener("click", () =>
      changeQuantity(prod.id_producto, -1, qtyInput)
    );
    plusBtn.addEventListener("click", () =>
      changeQuantity(prod.id_producto, 1, qtyInput)
    );
    qtyInput.addEventListener("change", () =>
      changeQuantity(prod.id_producto, 0, qtyInput, true)
    );
    removeBtn.addEventListener("click", () =>
      removeItem(prod.id_producto, row)
    );
  }

  // --- Cambiar cantidad ---
  function changeQuantity(id, delta, input, fromInput = false) {
    let newQty = fromInput
      ? parseInt(input.value)
      : parseInt(input.value) + delta;
    if (isNaN(newQty) || newQty < 1) newQty = 1;
    input.value = newQty;

    fetch(
      "/OptimizaTuNegocio/OptimizaTuNegocio/Pages/administracion/carrito_actions.php",
      {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `action=update&id_producto=${id}&cantidad=${newQty}`, // 🔥 antes era add
      }
    )
      .then((res) => res.json())
      .then(() => updateRowTotal(input))
      .catch((err) => console.error("Error actualizando cantidad:", err));
  }

  // --- Actualizar total por fila ---
  function updateRowTotal(input) {
    const row = input.closest("tr");
    const price =
      parseInt(
        row.querySelector(".item-price").textContent.replace(/\D/g, "")
      ) || 0;
    const qty = parseInt(input.value);
    row.querySelector(".item-total").textContent = formatCurrency(price * qty);
    updateCartSummary();
  }

  // --- Eliminar producto ---
  function removeItem(id, row) {
    if (!confirm("¿Eliminar este producto?")) return;
    fetch(
      "/OptimizaTuNegocio/OptimizaTuNegocio/Pages/administracion/carrito_actions.php",
      {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `action=remove&id_producto=${id}`,
      }
    )
      .then((res) => res.json())
      .then(() => {
        row.remove();
        checkEmptyCart();
        updateCartSummary();
      })
      .catch((err) => console.error("Error eliminando producto:", err));
  }

  // --- Vaciar carrito ---
  window.clearCart = function () {
    if (!confirm("¿Vaciar carrito?")) return;
    fetch(
      "/OptimizaTuNegocio/OptimizaTuNegocio/Pages/administracion/carrito_actions.php",
      {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "action=clear",
      }
    )
      .then((res) => res.json())
      .then(() => {
        cartBody.innerHTML = "";
        checkEmptyCart();
        updateCartSummary();
      })
      .catch((err) => console.error("Error vaciando carrito:", err));
  };

  // --- Revisar carrito vacío ---
  function checkEmptyCart() {
    const emptyMessage = document.getElementById("empty-cart-message");
    const container = document.getElementById("cart-items-container");
    if (cartBody.children.length === 0) {
      container.classList.add("d-none");
      emptyMessage.classList.remove("d-none");
    } else {
      container.classList.remove("d-none");
      emptyMessage.classList.add("d-none");
    }
  }

  // --- Aplicar cupón ---
  window.applyCoupon = function () {
    const couponCode = document.getElementById("coupon-code").value.trim();
    if (couponCode) {
      alert(`Cupón "${couponCode}" aplicado (simulado)`);
    }
    updateCartSummary();
  };

  // --- Actualizar resumen ---
  function updateCartSummary() {
    let subtotal = 0;
    document.querySelectorAll(".item-total").forEach((td) => {
      let val = td.textContent.replace(/[^0-9]/g, ""); // quitar todo excepto números
      subtotal += Number(val) || 0;
    });

    const discount = 0; // si no aplicas cupones
    const total = subtotal - discount; // sin envío

    document.getElementById("subtotal").textContent = formatCurrency(subtotal);
    document.getElementById("discount").textContent = `-${formatCurrency(
      discount
    )}`;
    document.getElementById("cart-total").textContent = formatCurrency(total);

    const itemCount = cartBody.children.length;
    cartCount.textContent = `${itemCount} ${
      itemCount === 1 ? "producto" : "productos"
    }`;
  }

  // --- Formatear a CLP ---
  function formatCurrency(value) {
    return value.toLocaleString("es-CL", {
      style: "currency",
      currency: "CLP",
    });
  }

  // --- Inicializar ---
  loadCart();
  // --- Procesar venta ---
  const checkoutBtn = document.querySelector(".btn-checkout");

  checkoutBtn.addEventListener("click", () => {
    const termsCheck = document.getElementById("terms-check");
    if (!termsCheck.checked) {
      alert("Debes aceptar los términos y condiciones antes de pagar.");
      return;
    }

    const paymentMethod = document.getElementById("payment-method").value;

    if (!confirm("¿Confirmas realizar la compra?")) return;

    checkoutBtn.disabled = true;
    checkoutBtn.textContent = "Procesando...";

    // --- Recolectar productos del carrito ---
    const cartItems = Array.from(
      document.getElementById("cart-body").querySelectorAll("tr")
    ).map((row) => ({
      id_producto: row.dataset.id,
      cantidad: parseInt(row.querySelector(".quantity-input").value),
    }));

    if (cartItems.length === 0) {
      alert("El carrito está vacío.");
      checkoutBtn.disabled = false;
      checkoutBtn.innerHTML =
        '<i class="fas fa-credit-card me-2"></i> Proceder al pago';
      return;
    }

    // --- Preparar datos para enviar ---
    const data = new URLSearchParams();
    data.append("payment_method", paymentMethod);
    data.append("cart", JSON.stringify(cartItems));

    fetch(
      "/OptimizaTuNegocio/OptimizaTuNegocio/PHP/administracion/procesar_venta.php",
      {
        method: "POST",
        body: data,
      }
    )
      .then((res) => res.json())
      .then((data) => {
        if (data.status === "ok") {
          alert("Compra realizada con éxito!");
          window.location.reload(); // o redirigir a página de éxito
        } else {
          alert("Error al procesar la venta: " + data.mensaje);
        }
      })
      .catch((err) => {
        console.error("Error procesando venta:", err);
        alert("Error inesperado al procesar la venta.");
      })
      .finally(() => {
        checkoutBtn.disabled = false;
        checkoutBtn.innerHTML =
          '<i class="fas fa-credit-card me-2"></i> Proceder al pago';
      });
  });
});

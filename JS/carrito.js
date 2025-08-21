
// Función para actualizar la cantidad con los botones +/-
function updateQuantity(button, change) {
    const input = button.parentNode.querySelector('input');
    let newValue = parseInt(input.value) + change;

    if (newValue < 1) newValue = 1;
    if (newValue > 99) newValue = 99;

    input.value = newValue;
    updateTotal(input);
}

// Función para actualizar el total de un producto
function updateTotal(input) {
    const row = input.closest('tr');
    const price = parseInt(row.querySelector('.item-price').textContent.replace(/\D/g, ''));
    const qty = parseInt(input.value);
    const totalCell = row.querySelector('.item-total');
    totalCell.textContent = `$${(price * qty).toLocaleString()}`;
    updateCartSummary();
}

// Función para eliminar un producto
function removeItem(btn) {
    if (confirm('¿Estás seguro de que quieres eliminar este producto de tu carrito?')) {
        btn.closest('tr').remove();
        updateCartSummary();
        checkEmptyCart();
    }
}

// Función para vaciar el carrito
function clearCart() {
    if (confirm('¿Estás seguro de que quieres vaciar tu carrito?')) {
        document.querySelectorAll('#cart-body tr').forEach(row => row.remove());
        updateCartSummary();
        checkEmptyCart();
    }
}

// Función para verificar si el carrito está vacío
function checkEmptyCart() {
    const cartBody = document.getElementById('cart-body');
    const emptyMessage = document.getElementById('empty-cart-message');

    if (cartBody.children.length === 0) {
        document.getElementById('cart-items-container').classList.add('d-none');
        emptyMessage.classList.remove('d-none');
    } else {
        document.getElementById('cart-items-container').classList.remove('d-none');
        emptyMessage.classList.add('d-none');
    }
}

// Función para aplicar cupón
function applyCoupon() {
    const couponCode = document.getElementById('coupon-code').value;
    // Aquí iría la lógica para validar el cupón
    alert(`Cupón "${couponCode}" aplicado (simulado)`);
    updateCartSummary();
}

// Función para actualizar el resumen del carrito
function updateCartSummary() {
    let subtotal = 0;
    document.querySelectorAll('.item-total').forEach(td => {
        subtotal += parseInt(td.textContent.replace(/\D/g, ''));
    });

    const shippingCost = 5000; // Costo fijo de envío por ahora
    const discount = 0; // Podría calcularse si hay cupones

    document.getElementById('subtotal').textContent = `$${subtotal.toLocaleString()}`;
    document.getElementById('shipping-cost').textContent = `$${shippingCost.toLocaleString()}`;
    document.getElementById('discount').textContent = `-$${discount.toLocaleString()}`;
    document.getElementById('cart-total').textContent = `$${(subtotal + shippingCost - discount).toLocaleString()}`;

    // Actualizar contador de productos
    const itemCount = document.querySelectorAll('#cart-body tr').length;
    document.getElementById('cart-count').textContent = `${itemCount} ${itemCount === 1 ? 'producto' : 'productos'}`;
}

// Inicializar el carrito
updateCartSummary();

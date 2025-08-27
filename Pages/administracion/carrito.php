<?php
session_start();
include __DIR__ . '/../../PHP/administracion/navbar.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="./../../CSS/estilos_emprendedores.css">
    <link rel="stylesheet" href="./../../CSS/carrito.css">
</head>

<body>
    <div class="container py-4">
        <!-- Header Carrito -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0"><i class="fas fa-shopping-cart me-2 text-prueba"></i>Mi Carrito</h2>
            <span class="badge badge-cart-count rounded-pill px-3 py-2" id="cart-count">0 productos</span>
        </div>

        <div class="row">
            <!-- Lista de productos -->
            <div class="col-lg-8">
                <div class="card shadow-sm mb-4 border-0">
                    <div class="card-body p-4">
                        <div id="cart-items-container">
                            <div class="table-responsive">
                                <table class="table align-middle cart-table">
                                    <thead>
                                        <tr>
                                            <th style="border-radius: 8px 0 0 8px;">Producto</th>
                                            <th>Precio</th>
                                            <th>Cantidad</th>
                                            <th>Total</th>
                                            <th style="border-radius: 0 8px 8px 0;"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="cart-body">
                                        <!-- Productos se cargarán desde JS -->
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Carrito vacío -->
                        <div id="empty-cart-message" class="empty-cart d-none text-center">
                            <i class="fas fa-shopping-cart fa-3x mb-2"></i>
                            <h4 class="text-prueba">Tu carrito está vacío</h4>
                            <p class="text-muted">Agrega productos para continuar con tu compra</p>
                            <a href="/Pages/administracion/ver_productos.php" class="btn btn-prueba px-4">
                                <i class="fas fa-store me-2"></i>Ir a la tienda
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0">
                    <div class="card-body p-3 d-flex justify-content-between">
                        <a href="./../../../OptimizaTuNegocio/Pages/administracion/ver_productos.php" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i> Seguir comprando
                        </a>
                        <button class="btn btn-clear-cart" onclick="clearCart()">
                            <i class="fas fa-trash-alt me-2"></i> Vaciar carrito
                        </button>
                    </div>
                </div>
            </div>

            <!-- Resumen de compra -->
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 sticky-summary">
                    <div class="card-body cart-summary">
                        <h5 class="mb-3 text-prueba"><i class="fas fa-receipt me-2"></i>Resumen de compra</h5>

                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Subtotal:</span>
                            <span class="fw-bold" id="subtotal">0</span>
                        </div>

                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Descuento:</span>
                            <span class="fw-bold text-success" id="discount">0</span>
                        </div>

                        <div class="divider"></div>

                        <div class="d-flex justify-content-between fw-bold fs-5 mb-4">
                            <span class="text-prueba">Total:</span>
                            <span class="text-prueba" id="cart-total">0</span>
                        </div>

                        <div class="mb-3">
                            <label for="payment-method" class="form-label text-muted">Método de pago:</label>
                            <select id="payment-method" class="form-select border-prueba">
                                <option value="card">Tarjeta de crédito/débito</option>
                                <option value="transfer">Transferencia bancaria</option>
                                <option value="cash">Efectivo</option>
                                <option value="pse">PSE</option>
                            </select>
                        </div>

                        <div class="mb-3" id="coupon-section">
                            <label for="coupon-code" class="form-label text-muted">Cupón de descuento:</label>
                            <div class="input-group">
                                <input type="text" class="form-control border-prueba" id="coupon-code" placeholder="Ingresa tu cupón">
                                <button class="btn btn-outline-prueba" type="button" onclick="applyCoupon()">Aplicar</button>
                            </div>
                        </div>

                        <button class="btn btn-checkout w-100 mb-3">
                            <i class="fas fa-credit-card me-2"></i> Proceder al pago
                        </button>

                        <div class="form-check mt-3">
                            <input class="form-check-input" type="checkbox" id="terms-check">
                            <label class="form-check-label small text-muted" for="terms-check">
                                Acepto los <a href="#" class="text-prueba">términos y condiciones</a> y las <a href="#" class="text-prueba">políticas de privacidad</a>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="./../../JS/carrito.js"></script>
    
</body>

</html>

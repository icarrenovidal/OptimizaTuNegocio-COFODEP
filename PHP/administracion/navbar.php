<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$current_page = basename($_SERVER['PHP_SELF']);

function is_active($page, $current)
{
    return $page === $current ? 'active' : '';
}
?>
<link rel="stylesheet" href="./../../CSS/navbar.css">

<nav class="navbar cofodep-navbar">
    <div class="nav-container">
        <div class="nav-main-row">
            <!-- Logo y emprendimiento -->
            <div class="nav-brand brand-content">
                <?php if (!empty($_SESSION['emprendimiento_nombre'])): ?>
                    <img src="/OptimizaTuNegocio/OptimizaTuNegocio/<?= $_SESSION['emprendimiento_logo'] ?? 'icons/icono.png' ?>"
                        alt="Logo" class="emprendimiento-logo">
                    <span class="emprendimiento-nombre"><?= htmlspecialchars($_SESSION['emprendimiento_nombre']) ?></span>
                <?php endif; ?>
            </div>

            <!-- Enlaces de navegación -->
            <div class="nav-links">
                <a href="home_administracion.php" class="<?= is_active('home_administracion.php', $current_page) ?>">
                    <i class="fas fa-home"></i> Inicio
                </a>
                <a href="ver_productos.php" class="<?= is_active('ver_productos.php', $current_page) ?>">
                    <i class="fas fa-box-open"></i> Productos
                </a>
                <a href="agregar_productos.php" class="<?= is_active('agregar_productos.php', $current_page) ?>">
                    <i class="fas fa-list"></i> Agregar Productos
                </a>
            </div>

            <!-- Acciones de usuario -->
            <div class="nav-user-actions">
                <?php if (!empty($_SESSION['usuario_nombre'])): ?>
                    <div class="user-info">
                        <span class="usuario-nombre">
                            <?= htmlspecialchars($_SESSION['usuario_nombre'] . " " . ($_SESSION['usuario_apellido'] ?? '')) ?>
                        </span>
                    </div>
                <?php endif; ?>

                <a href="/OptimizaTuNegocio/OptimizaTuNegocio/auth/PHP/logout.php" class="logout-link">
                    <i class="fas fa-sign-out-alt"></i>
                </a>

                <a href="./../../Pages/administracion/carrito.php" class="cart-icon">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-count">0</span>
                </a>
            </div>

            <!-- Menú hamburguesa -->
            <button class="hamburger" aria-label="Menú" aria-expanded="false">
                <span></span><span></span><span></span>
            </button>
        </div>

        <!-- Menú móvil -->
        <div class="mobile-menu">
            <a href="home_administracion.php" class="<?= is_active('home_administracion.php', $current_page) ?>">
                <i class="fas fa-home"></i> Inicio
            </a>
            <a href="ver_productos.php" class="<?= is_active('ver_productos.php', $current_page) ?>">
                <i class="fas fa-box-open"></i> Productos
            </a>
            <a href="agregar_productos.php" class="<?= is_active('agregar_productos.php', $current_page) ?>">
                <i class="fas fa-list"></i> Agregar Productos
            </a>
            <a href="/OptimizaTuNegocio/OptimizaTuNegocio/auth/PHP/logout.php" class="logout-link">
                <i class="fas fa-sign-out-alt"></i> Cerrar sesión
            </a>
        </div>
    </div>
</nav>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const hamburger = document.querySelector('.hamburger');
        const mobileMenu = document.querySelector('.mobile-menu');

        hamburger.addEventListener('click', function() {
            this.classList.toggle('active');
            mobileMenu.classList.toggle('active');
            const isExpanded = this.getAttribute('aria-expanded') === 'true';
            this.setAttribute('aria-expanded', !isExpanded);
        });

        document.querySelectorAll('.mobile-menu a').forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 992) {
                    hamburger.classList.remove('active');
                    mobileMenu.classList.remove('active');
                    hamburger.setAttribute('aria-expanded', 'false');
                }
            });
        });

        // Confirmación de logout
        document.querySelectorAll('.logout-link').forEach(link => {
            link.addEventListener('click', function(e) {
                if (!confirm('¿Seguro que quieres cerrar sesión?')) {
                    e.preventDefault();
                }
            });
        });
    });

    function actualizarContadorCarrito(nuevoTotal) {
        const contador = document.querySelector('.cart-count');
        if (contador) {
            contador.textContent = nuevoTotal;
            contador.classList.add('animate-bounce');
            setTimeout(() => contador.classList.remove('animate-bounce'), 300);
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        // Inicializar contador al cargar
        fetch('/OptimizaTuNegocio/OptimizaTuNegocio/Pages/administracion/carrito_actions.php?action=list')
            .then(res => res.json())
            .then(data => {
                if (Array.isArray(data)) {
                    const total = data.reduce((sum, p) => sum + (p.cantidad || 0), 0);
                    actualizarContadorCarrito(total);
                }
            });
    });
</script>

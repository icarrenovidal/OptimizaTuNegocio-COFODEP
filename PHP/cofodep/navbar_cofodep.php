<?php
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
                    <img src="/OptimizaTuNegocio/OptimizaTuNegocio/Uploads/icons/<?= $_SESSION['emprendimiento_logo'] ?? 'icono.png' ?>"
                        alt="Logo" class="emprendimiento-logo">

                    <span class="emprendimiento-nombre"><?= htmlspecialchars($_SESSION['emprendimiento_nombre']) ?></span>
                <?php endif; ?>
            </div>

            <!-- Enlaces de navegación -->
            <div class="nav-links">
                <a href="home_cofodep.php" class="<?= is_active('home_cofodep.php', $current_page) ?>">
                    <i class="fas fa-home"></i> Inicio
                </a>
                <a href="./../../Pages/cofodep/crear_usuarios.php" class="<?= is_active('crear_usuarios.php', $current_page) ?>">
                    <i class="fas fa-user-plus"></i> Agregar emprendimiento
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
            </div>

            <!-- Menú hamburguesa -->
            <button class="hamburger" aria-label="Menú" aria-expanded="false">
                <span></span><span></span><span></span>
            </button>
        </div>

        <!-- Menú móvil -->
        <div class="mobile-menu">
            <a href="home_cofodep.php" class="<?= is_active('home_cofodep.php', $current_page) ?>">
                <i class="fas fa-home"></i> Inicio
            </a>
            <a href="./../../Pages/cofodep/crear_usuarios.php" class="<?= is_active('crear_usuarios.php', $current_page) ?>">
                <i class="fas fa-user-plus"></i> Agregar emprendimiento
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
</script>
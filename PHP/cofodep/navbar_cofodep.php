<?php
$current_page = basename($_SERVER['PHP_SELF']);

function is_active($page, $current)
{
    return $page === $current ? 'active' : '';
}
?>
<link rel="stylesheet" href="./../../CSS/navbar.css">

<nav class="navbar">
    <div class="nav-container">
        <div class="nav-header">
            <a href="index.php" class="logo">Optimiza<span>TuNegocio</span></a>
            <button class="hamburger" aria-label="Menú" aria-expanded="false">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>

        <div class="nav-links-container">
            <div class="nav-links">
                <a href="home_cofodep.php" class="<?= is_active('index.php', $current_page) ?>">
                    <i class="fas fa-home"></i> Inicio
                </a>
                <a href="/OptimizaTuNegocio/OptimizaTuNegocio/auth/PHP/logout.php" class="logout-link">
                    <i class="fas fa-sign-out-alt"></i> Cerrar sesión
                </a>
            </div>
        </div>
    </div>
</nav>




<script>
    // Funcionalidad del menú hamburguesa
    document.addEventListener('DOMContentLoaded', function() {
        const hamburger = document.querySelector('.hamburger');
        const navContainer = document.querySelector('.nav-links-container');

        hamburger.addEventListener('click', function() {
            this.classList.toggle('active');
            navContainer.classList.toggle('active');

            // Actualizar atributo ARIA
            const isExpanded = this.getAttribute('aria-expanded') === 'true';
            this.setAttribute('aria-expanded', !isExpanded);
        });

        // Cerrar menú al hacer clic en un enlace (en móviles)
        document.querySelectorAll('.nav-links a').forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 992) {
                    hamburger.classList.remove('active');
                    navContainer.classList.remove('active');
                    hamburger.setAttribute('aria-expanded', 'false');
                }
            });
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
    window.addEventListener('pageshow', function(event) {
        if (event.persisted || (window.performance && window.performance.getEntriesByType("navigation")[0].type === "back_forward")) {
            // Recargar página si viene del cache
            window.location.reload();
        }
    });
</script>
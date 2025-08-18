<?php
// navbar.php
$current_page = basename($_SERVER['PHP_SELF']);

function is_active($page, $current) {
    return $page === $current ? 'active' : '';
}
?>

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
                <a href="home_administracion.php" class="<?= is_active('index.php', $current_page) ?>">
                    <i class="fas fa-home"></i> Inicio
                </a>
                <a href="ver_productos.php" class="<?= is_active('ver_productos.php', $current_page) ?>">
                    <i class="fas fa-box-open"></i> Productos
                </a>
                <a href="agregar_productos.php" class="<?= is_active('agregar_productos.php', $current_page) ?>">
                    <i class="fas fa-list"></i> Agregar Productos
                </a>
                <a href="contact.php" class="<?= is_active('contact.php', $current_page) ?>">
                    <i class="fas fa-envelope"></i> Contacto
                </a>
            </div>
            
            <div class="nav-actions">
                <a href="./../../Pages/administracion/carrito.php" class="cart-icon">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-count">0</span>
                </a>
                <a href="login.php" class="login-btn">
                    <i class="fas fa-user"></i> Ingresar
                </a>
            </div>
        </div>
    </div>
</nav>

<style>
    /* Estilos del navbar con tu paleta */

    .navbar {
        background-color: var(--color1);
        color: white;
        padding: 15px 0;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        position: sticky;
        top: 0;
        z-index: 1000;
    }
    
    .nav-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }
    
    .nav-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .logo {
        font-size: 24px;
        font-weight: bold;
        color: white;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    
    .logo span {
        color: var(--color3);
        font-weight: 800;
    }
    
    .nav-links-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: all 0.3s ease;
    }
    
    .nav-links {
        display: flex;
        gap: 15px;
    }
    
    .nav-links a {
        color: white;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.3s ease;
        padding: 10px 15px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 17px;
    }
    
    .nav-links a:hover {
        background-color: rgba(255, 255, 255, 0.1);
        transform: translateY(-2px);
    }
    
    .nav-links a.active {
        background-color: var(--color2);
        color: white;
        font-weight: 600;
        box-shadow: 0 4px 8px rgba(0, 135, 94, 0.2);
    }
    
    .nav-links a i {
        font-size: 20px;
        color: var(--color4);
    }
    
    .nav-actions {
        display: flex;
        align-items: center;
        gap: 20px;
    }
    
    .cart-icon {
        position: relative;
        color: white;
        font-size: 18px;
        transition: all 0.3s;
    }
    
    .cart-icon:hover {
        transform: scale(1.1);
    }
    
    .cart-count {
        position: absolute;
        top: -8px;
        right: -8px;
        background-color: var(--color5);
        color: white;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        font-size: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
    }
    
    .login-btn {
        background-color: var(--color3);
        color: var(--color1);
        padding: 10px 20px;
        border-radius: 6px;
        text-decoration: none;
        transition: all 0.3s;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .login-btn:hover {
        background-color: var(--color4);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(245, 204, 23, 0.3);
    }
    
    /* Estilos del menú hamburguesa */
    .hamburger {
        display: none;
        background: none;
        border: none;
        cursor: pointer;
        padding: 10px;
        z-index: 1001;
    }
    
    .hamburger span {
        display: block;
        width: 25px;
        height: 3px;
        background-color: white;
        margin: 5px 0;
        transition: all 0.3s ease;
    }
    
    /* Estilos para móviles */
    @media (max-width: 992px) {
        .hamburger {
            display: block;
        }
        
        .nav-links-container {
            position: fixed;
            top: 0;
            left: -100%;
            width: 80%;
            max-width: 300px;
            height: 100vh;
            background-color: var(--color1);
            flex-direction: column;
            justify-content: flex-start;
            padding: 80px 20px 20px;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.2);
            z-index: 1000;
        }
        
        .nav-links {
            flex-direction: column;
            width: 100%;
            gap: 10px;
        }
        
        .nav-actions {
            margin-top: 30px;
            flex-direction: column;
            width: 100%;
            gap: 15px;
        }
        
        .login-btn {
            width: 100%;
            justify-content: center;
        }
        
        /* Cuando el menú está abierto */
        .nav-links-container.active {
            left: 0;
        }
        
        /* Animación del hamburguesa a X */
        .hamburger.active span:nth-child(1) {
            transform: rotate(45deg) translate(5px, 5px);
        }
        
        .hamburger.active span:nth-child(2) {
            opacity: 0;
        }
        
        .hamburger.active span:nth-child(3) {
            transform: rotate(-45deg) translate(7px, -6px);
        }
    }
    
    @media (max-width: 576px) {
        .logo {
            font-size: 20px;
        }
    }
</style>

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
</script>
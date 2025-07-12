<?php
require_once __DIR__ . '/../../../controllers/auth/verificar_sesion.php';
verificarAcceso(['Familiar']);

// Si no hay un usuario en la sesión redirigir al login
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../../index-login/htmls/index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tus Familiares - GeriCare Connect</title>
    <link rel="stylesheet" href="../../familiar/css_familiar/fami.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="../css_familiar/familiar_header.css?v=<?= time(); ?>">
</head>
<body>
    <?php
    if (isset($_SESSION['mensaje'])) {
        echo '<div class="alert alert-success" role="alert">' .
            '<span><i class="fas fa-check-circle"></i> ' . htmlspecialchars($_SESSION['mensaje']) . '</span>' .
            '<button type="button" class="alert-close-btn" onclick="this.parentElement.remove();">&times;</button>' .
            '</div>';
        unset($_SESSION['mensaje']);
    }
    if (isset($_SESSION['error'])) {
        echo '<div class="alert alert-danger" role="alert">' .
            '<span><i class="fas fa-exclamation-triangle"></i> ' . htmlspecialchars($_SESSION['error']) . '</span>' .
            '<button type="button" class="alert-close-btn" onclick="this.parentElement.remove();">&times;</button>' .
            '</div>';
        unset($_SESSION['error']);
    }
    ?>
    
    <header class="header-familiar animated fadeInDown">
    <div id="particles-js-header"></div>
    <div class="header-familiar-content">
        <a href="familiares.php" class="logo">
            <img src="../../imagenes/Geri_Logo-_blanco.png" alt="Logo GeriCare" class="logo-img">
            <span class="app-name">GeriCareConnect</span>
        </a>
        
        <nav class="user-navigation">
            <a href="../../../controllers/index-login/actualizar_controller.php?id=<?= $_SESSION['id_usuario'] ?>">
                <i class="fas fa-user-cog"></i> Mi Perfil
            </a>
            <a href="../../../controllers/familiar/logout.php">
                <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
            </a>
        </nav>
    </div>
</header>
        
    <main class="admin-content">
        <div class="pacientes-container animated fadeInUp">
            <h1 class="animated slideInLeft"><i class="fas fa-users"></i> Tus Familiares (Pacientes Asociados)</h1>
            <div class="search-container animated slideInRight">
                <form id="searchForm" action="javascript:void(0);">
                    <div class="search-input-wrapper">
                        <input type="search" id="busquedaInput" name="busqueda" placeholder="Buscar por nombre, apellido o cédula...">
                        <button type="button" class="clear-button" id="clearButton" title="Limpiar" style="display: none;">
                            <i class="fas fa-times"></i>
                        </button>
                        <button type="submit" class="search-button" title="Buscar">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>
            <!-- Contenedor para la lista de pacientes -->
            <ul id="pacientes-lista" class="paciente-list">
                <!-- Los resultados se cargan aquí -->
                <li class="paciente-item cargando"><i class="fas fa-spinner fa-spin"></i> Cargando pacientes...</li>
            </ul>
             <div id="detalle-paciente-popup" style="display: none; margin-top: 15px; padding: 10px; border: 1px solid #ccc; border-radius: 5px;"></div>
        </div>
    </main>
<script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
<script src="[https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js](https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js)"></script>
<script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
<script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const particlesContainer = document.getElementById('particles-js-header');
        if (particlesContainer) {
            particlesJS('particles-js-header', {
                "particles": {
                    "number": { "value": 80, "density": { "enable": true, "value_area": 800 } },
                    "color": { "value": "#ffffff" },
                    "shape": { "type": "circle" },
                    "opacity": { "value": 0.6, "random": true, "anim": { "enable": true, "speed": 1, "opacity_min": 0.1, "sync": false } },
                    "size": { "value": 3, "random": true },
                    "line_linked": { "enable": true, "distance": 150, "color": "#ffffff", "opacity": 0.4, "width": 1 },
                    "move": { "enable": true, "speed": 4, "direction": "none", "random": false, "straight": false, "out_mode": "out" }
                },
                "interactivity": {
                    "detect_on": "canvas",
                    "events": { "onhover": { "enable": true, "mode": "grab" }, "onclick": { "enable": true, "mode": "push" } },
                    "modes": { "grab": { "distance": 140, "line_linked": { "opacity": 1 } }, "push": { "particles_nb": 4 } }
                },
                "retina_detect": true
            });
        }
    });
</script>
    <script src="/GericareConnect/views/familiar/js_familiar/familiares_vista_copy.js"></script>
</body>
</html>
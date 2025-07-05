<?php
require_once __DIR__ . '/../../../controllers/auth/verificar_sesion.php';
verificarAcceso(['Cuidador']);

// Si no hay un usuario en la sesión redirige al login
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../../index-login/htmls/index.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pacientes Asignados - GeriCare Connect</title>
    <link rel="stylesheet" href="../../familiar/css_familiar/fami.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <style>
        .search-container { max-width: 100%; }
        .user-info { margin-right: 2rem; font-weight: 500; color: #555; }
        .pacientes-container h1 { color: #ffc107; }
        .paciente-item:hover { background-color: #fff8e1; }
        .search-input-wrapper input:focus { border-color: #ffc107; }
    </style>
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
    
    <header class="main-header animated fadeInDown">
        <div class="logo-container">
            <img src="../../imagenes/Geri_Logo-..png" alt="Logo de la aplicación" class="logo" onclick="window.location.href='cuidadores_panel_principal.php'">
            <span class="app-name">GERICARE CONNECT</span>

            <div class="user-info">
                <strong>Rol:</strong> <?= htmlspecialchars($_SESSION['nombre_rol'] ?? 'Cuidador') ?>
            </div>
        </div>
        
        <nav class="top-navigation">
             <ul>
                <li>
                    <a href="../../../controllers/index-login/actualizar_controller.php?id=<?= $_SESSION['id_usuario'] ?>">
                        <i class="fas fa-user-cog"></i> Mi Perfil
                    </a>
                </li>
                <li><a href="cuidadores_panel_principal.php" class="active"><i class="fas fa-user-injured"></i> Pacientes</a></li>
                <li><a href="gestion_entradas_salidas.php"><i class="fas fa-calendar-plus"></i> Entradas y Salidas</a></li>
                <li><a href="historia_clinica.php"><i class="fas fa-calendar-plus"></i> Historias Clinicas</a></li>
                <li><a href="cuidador_agregar_actividad.html"><i class="fas fa-calendar-plus"></i> Agregar Actividad</a></li>
                <li><a href="../../../controllers/cuidador/logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a></li>
            </ul>
        </nav>
        </header>
    <main class="admin-content">
        <div class="pacientes-container animated fadeInUp">
            <h1 class="animated slideInLeft"><i class="fas fa-users"></i> Pacientes Asignados</h1> 
            <div class="search-container animated slideInRight">
                <form id="searchForm" action="javascript:void(0);">
                    <div class="search-input-wrapper">
                        <input type="search" id="busquedaInput" name="busqueda" placeholder="Buscar por nombre, apellido o cédula...">
                        <button type="button" class="clear-button" id="clearButton" title="Limpiar" style="display: none;"><i class="fas fa-times"></i></button>
                        <button type="submit" class="search-button" title="Buscar"><i class="fas fa-search"></i></button>
                    </div>
                </form>
            </div>
             <ul id="pacientes-lista" class="paciente-list">
                <!-- Los resultados se cargarán aquí -->
                <li class="paciente-item cargando"><i class="fas fa-spinner fa-spin"></i> Cargando pacientes asignados...</li>
            </ul>
        </div>
    </main>

    <script src="../../cuidador/js_cuidador/cuidadores_panel_principal.js" defer></script>
</body>
</html>
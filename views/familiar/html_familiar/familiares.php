<?php
// Iniciar la sesión para poder acceder a las variables del usuario
session_start();

// Si no hay un usuario en la sesión redirigir al login
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
    <title>Tus Familiares - GeriCare Connect</title>
    <link rel="stylesheet" href="../../familiar/css_familiar/fami.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <?php
    if (isset($_SESSION['mensaje'])) {
        echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['mensaje']) . '</div>';
        unset($_SESSION['mensaje']);
    }
    if (isset($_SESSION['error'])) {
        echo '<div class="alert alert-danger">' . htmlspecialchars($_SESSION['error']) . '</div>';
        unset($_SESSION['error']);
    }
    ?>
    
    <header class="main-header animated fadeInDown">
        <div class="logo-container">
            <img src="../../imagenes/Geri_Logo-..png" alt="Logo de la aplicación" class="logo" onclick="window.location.href='familiares.php'">
            <span class="app-name">GERICARE CONNECT</span>

            <div class="user-info">
                <strong>Rol:</strong> <?= htmlspecialchars($_SESSION['nombre_rol'] ?? 'Familiar') ?>
            </div>
        </div>
        
        <nav class="top-navigation">
            <ul>
                <li>
                    <a href="../../../controllers/index-login/actualizar_controller.php?id=<?= $_SESSION['id_usuario'] ?>">
                        <i class="fas fa-user-cog"></i> Mi Perfil
                    </a>
                </li>
                <li><a href="enviar_solicitud.html"><i class="fas fa-envelope"></i> Enviar Solicitud</a></li>
                <li><a href="solicitudes_pendientes.html"><i class="fas fa-list-alt"></i> Solicitudes Pendientes</a></li>
                 <li><a href="../../../controllers/familiar/logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a></li>
            </ul>
        </nav>
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

    <script src="/GericareConnect/views/familiar/js_familiar/familiares_vista_copy.js"></script>
</body>
</html>
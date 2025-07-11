<?php
// Este archivo inicia la sesión y verifica el acceso.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../../controllers/auth/verificar_sesion.php';
verificarAcceso(['Cuidador']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo_pagina ?? 'Panel de Cuidador'; ?> - GeriCare Connect</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

    <link rel="stylesheet" href="../css_cuidador/cuidador_header.css?v=<?= time(); ?>">
    <link rel="stylesheet" href="../css_cuidador/cuidador_main.css?v=<?= time(); ?>">
    <link rel="stylesheet" href="../../familiar/css_familiar/fami.css?v=<?= time(); ?>">

    <style>
        /* Forzamos al header a estar siempre en la capa superior */
        header.header-cuidador {
            position: relative;
            z-index: 1000 !important; 
        }

        /* Forzamos al contenido principal a estar en una capa inferior */
        main.main-content {
            position: relative;
            z-index: 1 !important;
        }
    </style>

</head>
<body>

<header class="header-cuidador">
    <div id="particles-js-cuidador"></div>
    <div class="header-content">
        <a href="cuidadores_panel_principal.php" class="logo">
            <img src="../../imagenes/Geri_Logo-_blanco.png" alt="Logo GeriCare" class="logo-img">
            <h1>GeriCare Connect</h1>
        </a>
        
        <nav class="main-nav">
            <a href="cuidadores_panel_principal.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'cuidadores_panel_principal.php' ? 'active' : ''; ?>">
                <i class="fas fa-users"></i> Mis Pacientes
            </a>
            <a href="cuidador_actividades.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'cuidador_actividades.php' ? 'active' : ''; ?>">
                <i class="fas fa-calendar-alt"></i> Actividades
            </a>
        </nav>

        <div class="user-info">
            <div class="user-details">
                <span class="user-name"><?= htmlspecialchars($_SESSION['nombre_completo'] ?? 'Cuidador') ?></span>
                <span class="user-role"><?= htmlspecialchars($_SESSION['nombre_rol'] ?? 'Rol') ?></span>
            </div>
            <i class="fas fa-user-circle user-avatar"></i>
            <ul class="dropdown-menu">
                <li><a href="#"><i class="fas fa-user-cog"></i> Mi Perfil</a></li>
                <li><a href="../../../controllers/cuidador/logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a></li>
            </ul>
        </div>
    </div>
</header>
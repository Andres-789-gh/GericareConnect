<?php
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

<?php include_once 'header_cuidador.php'; ?>

<main class="main-content">
    <div class="content-container animated fadeInUp">
        
        <h1><i class="fas fa-users"></i> Pacientes Asignados</h1> 
        
        <div class="search-container">
            <form id="searchForm" action="javascript:void(0);">
                <div class="search-input-wrapper">
                    <input type="search" id="busquedaInput" name="busqueda" placeholder="Buscar por nombre, apellido o cédula...">
                    <button type="button" class="clear-button" id="clearButton" title="Limpiar" style="display: none;"><i class="fas fa-times"></i></button>
                    <button type="submit" class="search-button" title="Buscar"><i class="fas fa-search"></i></button>
                </div>
            </form>
        </div>

        <ul id="pacientes-lista" class="paciente-list">
            <li class="paciente-card-cargando"><i class="fas fa-spinner fa-spin"></i> Cargando pacientes...</li>
        </ul>

    </div>
</main>

<script src="../js_cuidador/cuidadores_panel_principal.js" defer></script>

<?php 
include 'footer_cuidador.php'; 
?>


</body>
</html>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Pacientes - GeriCare Connect</title>
    <link rel="stylesheet" href="../../admin/css_admin/admin_pacientes.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .paciente-icon { margin-right: 10px; color: #3498db; }
        .paciente-info { flex-grow: 1; margin-right: 15px; }
        .menu-icon { display: flex; align-items: center; gap: 15px; }
        .menu-icon i { cursor: pointer; transition: color 0.2s ease; font-size: 1.1rem; }
        .eliminar-paciente-icon { color: #dc3545; }
        .eliminar-paciente-icon:hover { color: #c82333 !important; transform: scale(1.1); }
        .paciente-item.no-data { color: #777; justify-content: center; font-style: italic; }
        .paciente-item.cargando, .paciente-item.error { justify-content: center; cursor: default; font-style: italic; }
        .paciente-item.cargando:hover, .paciente-item.error:hover { background-color: inherit; transform: none; box-shadow: none;}
        .paciente-item.error { background-color: #f8d7da; color: #721c24; border-left: 5px solid #dc3545; }


        .notification-badge {
            background-color: #dc3545; 
            color: white;
            border-radius: 50%; 
            padding: 2px 6px; 
            font-size: 0.75em; 
            font-weight: bold;
            margin-left: 5px; 
            vertical-align: super; 
            min-width: 18px;
            text-align: center;
            line-height: 1;
            display: inline-block; 
        }
        .notification-badge:empty {
            display: none; 
        }
        .user-info {
            margin-left: 20px;
        }
    </style>
</head>
<body>
    <?php
    session_start();
    if (isset($_SESSION['mensaje'])) {
        echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['mensaje']) . '</div>';
        unset($_SESSION['mensaje']);
    }
    if (isset($_SESSION['error'])) {
        echo '<div class="alert alert-danger">' . htmlspecialchars($_SESSION['error']) . '</div>';
        unset($_SESSION['error']);
    }
    ?>
    <header class="admin-header animated fadeInDown">
        <div class="logo-container">
            <img src="../../imagenes/Geri_Logo-..png" alt="Logo de la aplicación" class="logo" onclick="window.location.href='admin_pacientes.php'">
            <span class="app-name">GERICARE CONNECT</span>
        </div>
        <div class="user-info">
            <strong>Rol:</strong> <?= htmlspecialchars($_SESSION['nombre_rol'] ?? 'Desconocido') ?>
        </div>
        <nav>
            <ul>
                <li>
                    <a href="../../../controllers/index-login/actualizar_controller.php?id=<?= $_SESSION['id_usuario'] ?>">
                        <i class="fas fa-user-cog"></i> Mi Perfil
                    </a>
                </li>
                <li><a href="admin_pacientes.php" class="active"><i class="fas fa-user-injured"></i> Pacientes</a></li>
                <li>
                    <a href="admin_solicitudes.html">
                        <i class="fas fa-envelope-open-text"></i> Solicitudes
                        <span class="notification-badge" id="solicitudes-badge"></span>
                    </a>
                </li>
                <li><a href="../../../controllers/admin/logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a></li>
            </ul>
        </nav>
        <div class="add-button-container">
            <a href="agregar_paciente.html" class="add-paciente-button">
                <i class="fas fa-user-plus"></i> Agregar Paciente
            </a>

            <a href="registrar_empleado.php" class="add-empleado-button">
                <i class="fas fa-user-tie"></i> Registrar Empleado
            </a>
        </div>
    </header>
    <main class="admin-content">
        <div class="pacientes-container animated fadeInUp">
            <h1 class="animated slideInLeft"><i class="fas fa-search"></i> Búsqueda De Usuarios Y Pacientes</h1>
            <div class="search-container animated slideInRight">
                <!-- Formulario Búsqueda Global -->
                <form id="universalSearchForm" class="universal-search-container">
                    <select name="filtro_rol" id="filtro_rol">
                        <option value="">Buscar en Todos</option>
                        <option value="Paciente">Pacientes</option>
                        <option value="Cuidador">Cuidadores</option>
                        <option value="Familiar">Familiares</option>
                        <option value="Administrador">Administradores</option>
                    </select>
                    <input type="search" id="termino_busqueda" name="busqueda" placeholder="Buscar por nombre, apellido o cédula...">
                </form>
            </div>
                <!-- Contenedor para mostrar los resultados -->
                <ul id="resultsContainer" class="results-list">
                    <li class="result-item" style="justify-content: center; color: #777;">
                        Use el buscador para encontrar usuarios o pacientes.
                    </li>
                </ul>
        </div>
    </main>

    <script src="../../admin/js_admin/admin_pacientes_copy.js" defer></script>

</body>
</html>
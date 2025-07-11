<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Pacientes - GeriCare Connect</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    
    <link rel="stylesheet" href="../css_admin/admin_main.css?v=<?= time(); ?>">
    <link rel="stylesheet" href="../css_admin/admin_pacientes.css?v=<?= time(); ?>">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body data-id-admin="<?= htmlspecialchars($_SESSION['id_usuario'] ?? 0) ?>">

    <header class="header">
        <div id="particles-js"></div>
        <div class="header-content animate__animated animate__fadeIn">
            <div class="logo" onclick="window.location.href='admin_pacientes.php'">
                <img src="../../imagenes/Geri_Logo-_blanco.png" alt="Logo GeriCare" class="logo-img">
                <h1>GeriCareConnect</h1>
            </div>
            <nav class="main-nav">
                <a href="admin_pacientes.php" class="active"><i class="fas fa-user-injured"></i> Pacientes</a>
                <a href="historia_clinica.php"><i class="fas fa-notes-medical"></i> Historias Clínicas</a>
                <a href="admin_actividades.php"><i class="fas fa-calendar-alt"></i> Actividades</a>
            </nav>
            <div class="user-actions">
                <a href="registrar_empleado.php" class="btn-header-action"><i class="fas fa-user-tie"></i> Registrar Empleado</a>
                 <div class="user-info">
                    <div class="user-details">
                        <span class="user-name"><?= htmlspecialchars($_SESSION['nombre_usuario'] ?? 'Usuario') ?></span>
                        <span class="user-role"><?= htmlspecialchars($_SESSION['nombre_rol'] ?? 'Desconocido') ?></span>
                    </div>
                    <i class="fas fa-user-circle user-avatar"></i>
                    <div class="dropdown-menu">
                        <a href="../../../controllers/index-login/actualizar_controller.php?id=<?= $_SESSION['id_usuario'] ?>"><i class="fas fa-user-cog"></i> Mi Perfil</a>
        
                    
                        </div>
                </div>
            </div>
        </div>
    </header>

    <!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Pacientes - GeriCare Connect</title>
    <link rel="stylesheet" href="../css_admin/admin_pacientes.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- ESTILOS "PRO" PARA LA TABLA DE RESULTADOS -->
    <style>
        .table-container { margin-top: 1.5rem; overflow-x: auto; }
        .results-table { width: 100%; border-collapse: collapse; background-color: white; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .results-table th, .results-table td { padding: 14px 16px; text-align: left; border-bottom: 1px solid #e9ecef; }
        .results-table th { background-color: #007bff; color: white; font-size: 0.9em; text-transform: uppercase; }
        .results-table tbody tr:hover { background-color: #f8f9fa; }
        .actions a, .actions button { color: #333; margin: 0 8px; background: none; border: none; cursor: pointer; font-size: 1.1rem; transition: transform 0.2s; }
        .actions a:hover { color: #007bff; transform: scale(1.2); }
        .actions button:hover { color: #dc3545; transform: scale(1.2); }
        .genero-masculino, .genero-femenino { font-weight: 500; display: inline-flex; align-items: center; gap: 8px; padding: 4px 10px; border-radius: 15px; color: white; font-size: 0.9em; }
        .genero-masculino { background-color: #0d6efd; }
        .genero-femenino { background-color: #d63384; }
        .rol-tag { padding: 4px 10px; border-radius: 15px; color: white; font-size: 0.8em; font-weight: bold; text-transform: uppercase; }
        .rol-paciente { background-color: #198754; }
        .rol-cuidador { background-color: #ffc107; color: black; }
        .rol-familiar { background-color: #0dcaf0; }
        .rol-administrador { background-color: #6c757d; }
    </style>
</head>
<body data-id-admin="<?= htmlspecialchars($_SESSION['id_usuario'] ?? 0) ?>">
    <header class="admin-header">
        <div class="logo-container">
            <img src="../../imagenes/Geri_Logo-..png" alt="Logo de la aplicación" class="logo" onclick="window.location.href='admin_pacientes.php'">
            <span class="app-name">GERICARE CONNECT</span>
            <div class="user-info"><strong>Rol:</strong> <?= htmlspecialchars($_SESSION['nombre_rol'] ?? 'Desconocido') ?></div>
        </div>
        <nav>
            <ul>
                <li><a href="../../../controllers/index-login/actualizar_controller.php?id=<?= $_SESSION['id_usuario'] ?>"><i class="fas fa-user-cog"></i> Mi Perfil</a></li>
                <li><a href="admin_pacientes.php" class="active"><i class="fas fa-user-injured"></i> Pacientes</a></li>
                <li><a href="historia_clinica.php"><i class="fas fa-envelope-open-text"></i> Historias Clinicas</a></li>
                <li><a href="admin_actividades.php"><i class="fas fa-envelope-open-text"></i> Actividades</a></li>
                <li><a href="../../../controllers/admin/logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a></li>
            </ul>
        </nav>
        <div class="add-button-container">
            <a href="agregar_paciente.php" class="add-paciente-button"><i class="fas fa-user-plus"></i> Agregar Paciente</a>
            <a href="registrar_empleado.php" class="add-empleado-button"><i class="fas fa-user-tie"></i> Registrar Empleado</a>
        </div>
    </header>

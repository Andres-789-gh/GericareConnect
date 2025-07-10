<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Actividades - GeriCare Connect</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
                <a href="admin_pacientes.php"><i class="fas fa-user-injured"></i> Pacientes</a>
                <a href="historia_clinica.php"><i class="fas fa-notes-medical"></i> Historias Clínicas</a>
                <a href="admin_actividades.php" class="active"><i class="fas fa-calendar-alt"></i> Actividades</a>
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
                        <a href="../../../controllers/admin/logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="main-content">
        <div class="content-container">
            <div class="content-header">
                <h1>Listado de Actividades</h1>
                <a href="form_actividades.php" class="btn-add-activity"><i class="fas fa-plus"></i> Asignar Actividad</a>
            </div>

            <div class="table-responsive-container">
                <table class="activities-table">
                    <thead>
                        <tr>
                            <th>Paciente</th>
                            <th>Cuidador</th>
                            <th>Descripción de la Actividad</th>
                            <th>Fecha y Hora</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Ana Sofía Torres</td>
                            <td>Carlos Alberto Ruiz</td>
                            <td>Paseo matutino de 30 minutos en el jardín.</td>
                            <td>10/07/2024 09:00 AM</td>
                            <td><span class="status status-completada">Completada</span></td>
                            <td class="actions">
                                <button class="btn-action btn-edit"><i class="fas fa-pencil-alt"></i></button>
                                <button class="btn-action btn-delete"><i class="fas fa-trash-alt"></i></button>
                            </td>
                        </tr>
                        <tr>
                            <td>Luis Fernando Vega</td>
                            <td>Marta Elena Gómez</td>
                            <td>Toma de presión arterial y registro en bitácora.</td>
                            <td>10/07/2024 10:00 AM</td>
                            <td><span class="status status-pendiente">Pendiente</span></td>
                            <td class="actions">
                                <button class="btn-action btn-edit"><i class="fas fa-pencil-alt"></i></button>
                                <button class="btn-action btn-delete"><i class="fas fa-trash-alt"></i></button>
                            </td>
                        </tr>
                         <tr>
                            <td>Jorge Isaac Mendoza</td>
                            <td>Lucía Fernanda Paz</td>
                            <td>Administrar medicamento para la diabetes.</td>
                            <td>10/07/2024 11:30 AM</td>
                            <td><span class="status status-cancelada">Cancelada</span></td>
                            <td class="actions">
                                <button class="btn-action btn-edit"><i class="fas fa-pencil-alt"></i></button>
                                <button class="btn-action btn-delete"><i class="fas fa-trash-alt"></i></button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
    
    <script src="../js_admin/admin_scripts.js"></script>
</body>
</html>
<?php session_start(); ?>
<!DOCTYPE html><html lang="es">
<head>
    <meta charset="UTF-8"><title>Dashboard - GeriCare Connect</title>
    <link rel="stylesheet" href="../css_admin/admin_main.css?v=<?= time(); ?>">
    <link rel="stylesheet" href="../libs/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../libs/animate/animate.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
</head>
<body data-id-admin="<?= htmlspecialchars($_SESSION['id_usuario'] ?? 0) ?>">
    <header class="admin-header">
        <div class="logo-container"><img src="../../imagenes/Geri_Logo-..png" alt="Logo" class="logo"><span class="app-name">GERICARE CONNECT</span></div>
        <nav>
            <ul>
                <li><a href="admin_pacientes.php" class="active"><i class="fas fa-users"></i> Usuarios</a></li>
                <li><a href="historia_clinica.php"><i class="fas fa-file-medical"></i> Historias Clínicas</a></li>
                <li><a href="admin_actividades.php"><i class="fas fa-tasks"></i> Actividades</a></li>
                <li><a href="../../../controllers/admin/logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a></li>
            </ul>
        </nav>
        <div class="add-button-container">
            <a href="agregar_paciente.php" class="btn-header btn-add-p"><i class="fas fa-user-plus"></i> Agregar Paciente</a>
            <a href="registrar_empleado.php" class="btn-header btn-add-e"><i class="fas fa-user-tie"></i> Registrar Empleado</a>
        </div>
    </header>
    <main class="main-content">
        <div class="content-container">
            <h1 class="animate__animated animate__fadeInDown">Búsqueda de Usuarios y Pacientes</h1>
            <div class="card search-card animate__animated animate__fadeInUp">
                <form id="universalSearchForm">
                    <div class="input-group">
                        <select name="filtro_rol" id="filtro_rol" class="form-select" style="max-width: 200px;">
                            <option value="">Buscar en Todos</option><option value="Paciente">Pacientes</option><option value="Cuidador">Cuidadores</option><option value="Familiar">Familiares</option>
                        </select>
                        <input type="search" id="termino_busqueda" name="busqueda" class="form-control" placeholder="Buscar por nombre, apellido o cédula...">
                    </div>
                </form>
            </div>
            <div id="resultsContainer" class="table-container animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">
                </div>
        </div>
    </main>
    <script src="../js_admin/admin_pacientes_copy.js" defer></script>
    <script src="../js_admin/admin_scripts.js"></script>
</body>
</html>
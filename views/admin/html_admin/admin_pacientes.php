<?php
session_start();
// Tu código de seguridad y sesión...
?>
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
                <img src="../../imagenes/Geri_Logo-..png" alt="Logo GeriCare" class="logo-img">
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
                        <a href="../../../controllers/admin/logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
             <h1 class="mb-0"><i class="fas fa-users"></i> Búsqueda de Usuarios</h1>
             <a href="agregar_paciente.php" class="btn-main-action">
                <i class="fas fa-plus"></i> Agregar Paciente
            </a>
        </div>
        
        <div class="search-container">
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
        
        <div id="resultsContainer" class="mt-4">
            <p style="text-align:center; color: #777;">Usa el buscador para encontrar usuarios o pacientes.</p>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    <script src="../js_admin/admin_scripts.js"></script>
    <script src="../js_admin/admin_pacientes_copy.js" defer></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        <?php if(isset($_SESSION['mensaje'])): ?>
            Swal.fire({ icon: 'success', title: '¡Éxito!', text: '<?= addslashes($_SESSION['mensaje']) ?>', timer: 3000, showConfirmButton: false });
            <?php unset($_SESSION['mensaje']); ?>
        <?php endif; ?>
        <?php if(isset($_SESSION['error'])): ?>
            Swal.fire({ icon: 'error', title: 'Oops...', text: '<?= addslashes($_SESSION['error']) ?>' });
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
    });
    </script>
</body>
</html>
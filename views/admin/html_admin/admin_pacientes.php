<?php
session_start();
// Tu código de seguridad y sesión...
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administrador - GeriCare Connect</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link rel="stylesheet" href="../css_admin/admin_header.css?v=<?= time(); ?>">
    <link rel="stylesheet" href="../css_admin/admin_pacientes.css?v=<?= time(); ?>">
    <link rel="stylesheet" href="../css_admin/admin_main.css?v=<?= time(); ?>">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- ESTILOS "PRO" PARA LA TABLA DE RESULTADOS -->
    <style>
         body {  font-family: 'Sans-serif', sans-serif; background-color: #f4f7f9; margin: 0; color: #333; }
        .admin-header { background-color: #fff; padding: 1rem 2rem; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e9ecef; }
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
</head>
<body data-id-admin="<?= htmlspecialchars($_SESSION['id_usuario'] ?? 0) ?>">

    <header class="header">
        <div id="particles-js"></div>
        
        <div class="header-content animate__animated animate__fadeIn">
            <a href="admin_pacientes.php" class="logo">
                <img src="../../imagenes/Geri_Logo-_blanco.png" alt="Logo GeriCare" class="logo-img">
                <h1>GeriCareConnect</h1>
            </a>
            
            <nav class="main-nav">
                <a href="admin_pacientes.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'admin_pacientes.php' ? 'active' : ''; ?>"><i class="fas fa-user"></i> Usuarios</a>
                <a href="historia_clinica.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'historia_clinica.php' ? 'active' : ''; ?>"><i class="fas fa-notes-medical"></i> Historias Clínicas</a>
                <a href="admin_actividades.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'admin_actividades.php' ? 'active' : ''; ?>"><i class="fas fa-calendar-alt"></i> Actividades</a>
            </nav>

            <div class="user-actions">
                <a href="registrar_empleado.php" class="btn-header-action"><i class="fas fa-user-tie"></i> Registrar Empleado</a>
                
                <div class="user-info">
                    <div class="user-details">
                        <span class="user-name"><?= htmlspecialchars($_SESSION['nombre_completo'] ?? 'Admin') ?></span>
                        <span class="user-role"><?= htmlspecialchars($_SESSION['nombre_rol'] ?? 'Administrador') ?></span>
                    </div>
                    <i class="fas fa-user-circle user-avatar"></i>
                    <ul class="dropdown-menu">
                        <li><a href="../../../controllers/index-login/actualizar_controller.php?id=<?= $_SESSION['id_usuario'] ?>"><i class="fas fa-user-cog"></i> Mi Perfil</a></li>
                        <li><a href="../../../controllers/admin/logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </header>
    <main class="admin-content">
        <div class="pacientes-container">
            <h1><i class="fas fa-search"></i> Búsqueda De Usuarios Y Pacientes</h1>
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
            
            <div id="resultsContainer">
                <p style="text-align:center; color: #777;">Use el buscador para encontrar usuarios o pacientes.</p>
            </div>
        </div>
    </main>
 <a href="agregar_paciente.php" class="floating-add-button" title="Agregar Nuevo Paciente"><i class="fas fa-plus"></i></a>
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
        <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    <script src="../js_admin/admin_scripts.js"></script>
    <script src="../js_admin/admin_pacientes_copy.js" defer></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const userInfo = document.querySelector('.user-info');
        if (userInfo) {
            userInfo.addEventListener('click', function(event) {
                event.stopPropagation();
                const dropdownMenu = this.querySelector('.dropdown-menu');
                if (dropdownMenu) {
                    dropdownMenu.classList.toggle('show');
                }
            });
        }
        window.addEventListener('click', function() {
            const openDropdown = document.querySelector('.dropdown-menu.show');
            if (openDropdown) {
                openDropdown.classList.remove('show');
            }
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const userInfo = document.querySelector('.user-info');
        
        if (userInfo) {
            userInfo.addEventListener('click', function(event) {
                // Detiene la propagación para que el clic no cierre el menú inmediatamente
                event.stopPropagation();
                
                // Busca el menú desplegable dentro del elemento clickeado
                const dropdownMenu = this.querySelector('.dropdown-menu');
                if (dropdownMenu) {
                    // Alterna la clase 'show' para mostrar u ocultar el menú
                    dropdownMenu.classList.toggle('show');
                }
            });
        }

        // Cierra el menú si se hace clic en cualquier otro lugar de la página
        window.addEventListener('click', function() {
            const openDropdown = document.querySelector('.dropdown-menu.show');
            if (openDropdown) {
                openDropdown.classList.remove('show');
            }
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const userInfo = document.querySelector('.user-info');
        
        if (userInfo) {
            userInfo.addEventListener('click', function(event) {
                // Detiene la propagación para que el clic no cierre el menú inmediatamente
                event.stopPropagation();
                
                // Busca el menú desplegable dentro del elemento clickeado
                const dropdownMenu = this.querySelector('.dropdown-menu');
                if (dropdownMenu) {
                    // Alterna la clase 'show' para mostrar u ocultar el menú
                    dropdownMenu.classList.toggle('show');
                }
            });
        }

        // Cierra el menú si se hace clic en cualquier otro lugar de la página
        window.addEventListener('click', function() {
            const openDropdown = document.querySelector('.dropdown-menu.show');
            if (openDropdown) {
                openDropdown.classList.remove('show');
            }
        });
    });
</script>
</body>
</html>
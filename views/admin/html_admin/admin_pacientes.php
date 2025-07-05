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
    <!-- TU HEADER ORIGINAL (NO SE TOCA) -->
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
                <li><a href="admin_solicitudes.php"><i class="fas fa-envelope-open-text"></i> Solicitudes</a></li>
                <li><a href="../../../controllers/admin/logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a></li>
            </ul>
        </nav>
        <div class="add-button-container">
            <a href="agregar_paciente.php" class="add-paciente-button"><i class="fas fa-user-plus"></i> Agregar Paciente</a>
            <a href="registrar_empleado.php" class="add-empleado-button"><i class="fas fa-user-tie"></i> Registrar Empleado</a>
        </div>
    </header>

    <main class="admin-content">
        <div class="pacientes-container">
            <!-- TU BÚSQUEDA GLOBAL ORIGINAL (NO SE TOCA) -->
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
            
            <!-- EL CONTENEDOR DONDE EL JS DIBUJARÁ LA TABLA -->
            <div id="resultsContainer">
                <p style="text-align:center; color: #777;">Use el buscador para encontrar usuarios o pacientes.</p>
            </div>
        </div>
    </main>

    <!-- TU SCRIPT ORIGINAL (APUNTA AL JS CORREGIDO) -->
    <script src="../js_admin/admin_pacientes_copy.js" defer></script>
    <script>
        // TU SCRIPT DE SWEETALERT ORIGINAL (NO SE TOCA)
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





<ksfajñhñwHFÑ>
<?php
// Tu código PHP existente para iniciar sesión y mostrar errores...
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Pacientes - GeriCare Connect</title>
    <link rel="stylesheet" href="../css_admin/admin_pacientes.css"> <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <header class="admin-header">
        <div class="logo-container">
            <img src="../../imagenes/Geri_Logo-..png" alt="Logo de la aplicación" class="logo" onclick="window.location.href='admin_pacientes.php'">
            <span class="app-name">GERICARE CONNECT</span>
            <div class="user-info">
                <strong>Rol:</strong> <?= htmlspecialchars($_SESSION['nombre_rol'] ?? 'Desconocido') ?>
            </div>
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
            <a href="agregar_paciente.php" class="add-paciente-button">
                <i class="fas fa-user-plus"></i> Agregar Paciente
            </a>
            <a href="registrar_empleado.php" class="add-empleado-button">
                <i class="fas fa-user-tie"></i> Registrar Empleado
            </a>
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
            <ul id="resultsContainer" class="results-list">
                <li class="result-item" style="justify-content: center; color: #777;">
                    Use el buscador para encontrar usuarios o pacientes.
                </li>
            </ul>
        </div>
    </main>

    <script src="../js_admin/admin_pacientes_copy.js" defer></script>
    <script>
        // Script para mostrar las notificaciones de éxito o error con SweetAlert
        document.addEventListener('DOMContentLoaded', function() {
            <?php if(isset($_SESSION['mensaje'])): ?>
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: '<?= addslashes($_SESSION['mensaje']) ?>',
                    timer: 3000,
                    showConfirmButton: false
                });
                <?php unset($_SESSION['mensaje']); ?>
            <?php endif; ?>

            <?php if(isset($_SESSION['error'])): ?>
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: '<?= addslashes($_SESSION['error']) ?>'
                });
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
        });
    </script>
</body>
</html>
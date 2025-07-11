<?php
require_once __DIR__ . '/../../../controllers/auth/verificar_sesion.php';
require_once __DIR__ . '/../../../models/clases/actividad.php';
require_once __DIR__ . '/../../../models/clases/usuario.php';

verificarAcceso(['Administrador']);

// --- Lógica del Reporte ---
$modelo_actividad = new Actividad();
$modelo_usuario = new usuario();

$cuidadores = $modelo_usuario->obtenerUsuariosPorRol('Cuidador');

$id_cuidador_filtro = $_GET['cuidador'] ?? '';
$busqueda = $_GET['busqueda'] ?? '';
$estado_filtro = $_GET['estado'] ?? '';

$actividades = [];
if (!empty($id_cuidador_filtro)) {
    $actividades = $modelo_actividad->consultarPorCuidador($id_cuidador_filtro, $busqueda, $estado_filtro);
}
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
    <link rel="stylesheet" href="../css_admin/a.css?v=<?= time(); ?>">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="../css_admin/historia_clinica_lista.css">
    <link rel="stylesheet" href="../css_admin/historia_clinica_lista.css">
      <link rel="stylesheet" href="../css_admin/admin_main.css?v=<?= time(); ?>">
    <link rel="stylesheet" href="../css_admin/historia_clinica_lista.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .search-container form { display: flex; gap: 15px; flex-wrap: wrap; }
        .search-container .form-group { display: flex; flex-direction: column; flex-grow: 1; }
        .search-container label { font-size: 0.9em; color: #555; margin-bottom: 5px; }
        .search-container input, .search-container select { padding: 12px; border: 1px solid #ccc; border-radius: 5px; font-size: 1rem; outline: none; }
        .search-container input { min-width: 250px; }
        .search-container button { align-self: flex-end; }
        .report-subtitle { text-align: center; color: #6c757d; margin-top: 2rem; font-style: italic; }

        /* ===== ESTILOS PARA EL NUEVO BOTÓN ===== */
        .btn-export {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            font-size: 1rem;
            font-weight: 500;
            color: white;
            background-color: #1D6F42; /* Un verde oscuro de Excel */
            border: none;
            border-radius: 8px;
            text-decoration: none;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease, box-shadow 0.3s ease;
            box-shadow: 0 2px 5px rgba(0,0,0,0.15);
        }
        .btn-export:hover {
            background-color: #165934; /* Un tono más oscuro al pasar el mouse */
            transform: translateY(-2px); /* Efecto de levantar el botón */
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }
        /* ======================================= */

    </style>
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
        <div class="historias-container">
            <h1><i class="fas fa-chart-line"></i> Reporte de Actividades por Cuidador</h1>
            
            <div class="search-container">
                <form method="GET">
                    <div class="form-group">
                        <label for="cuidador">Seleccione un Cuidador:</label>
                        <select name="cuidador" id="cuidador" onchange="this.form.submit()">
                            <option value="">-- Todos los Cuidadores --</option>
                            <?php foreach ($cuidadores as $cuidador): ?>
                                <option value="<?= $cuidador['id_usuario'] ?>" <?= ($id_cuidador_filtro == $cuidador['id_usuario']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cuidador['nombre'] . ' ' . $cuidador['apellido']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="estado">Filtrar por Estado:</label>
                        <select name="estado" id="estado" onchange="this.form.submit()">
                            <option value="">Todos</option>
                            <option value="Pendiente" <?= $estado_filtro == 'Pendiente' ? 'selected' : '' ?>>Pendientes</option>
                            <option value="Completada" <?= $estado_filtro == 'Completada' ? 'selected' : '' ?>>Completadas</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="busqueda">Buscar en resultados:</label>
                        <input type="search" name="busqueda" id="busqueda" placeholder="Por paciente o actividad..." value="<?= htmlspecialchars($busqueda) ?>">
                    </div>
                    <button type="submit"><i class="fas fa-search"></i> Filtrar</button>
                </form>
            </div>

            <?php if (!empty($id_cuidador_filtro)): ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Actividad</th>
                                <th>Paciente Asignado</th>
                                <th>Fecha</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($actividades)): ?>
                                <tr><td colspan="4">No se encontraron actividades para este cuidador con los filtros seleccionados.</td></tr>
                            <?php else: ?>
                                <?php foreach ($actividades as $actividad): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($actividad['tipo_actividad']) ?></td>
                                        <td><?= htmlspecialchars($actividad['nombre_paciente']) ?></td>
                                        <td><?= htmlspecialchars(date("d/m/Y", strtotime($actividad['fecha_actividad']))) ?></td>
                                        <td>
                                            <span class="estado-<?= strtolower(htmlspecialchars($actividad['estado_actividad'])) ?>">
                                                <?= htmlspecialchars($actividad['estado_actividad']) ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>

                    <div style="text-align: right; margin-top: 20px;">
                        <a href="../../../controllers/admin/actividad/exportar_actividades_cuidador.php?cuidador=<?= htmlspecialchars($id_cuidador_filtro) ?>&estado=<?= htmlspecialchars($estado_filtro) ?>&busqueda=<?= htmlspecialchars($busqueda) ?>" class="btn-export">
                            <i class="fas fa-file-excel"></i> Exportar a Excel
                        </a>
                    </div>
                    </div>
            <?php else: ?>
                <p class="report-subtitle">Por favor, seleccione un cuidador para generar el reporte.</p>
            <?php endif; ?>
        </div>
    </main>
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
        // Mensajes de éxito y error
        document.addEventListener('DOMContentLoaded', function() {
            <?php if(isset($_SESSION['mensaje'])): ?>
                Swal.fire({
                    title: '¡Éxito!',
                    text: '<?= addslashes($_SESSION['mensaje']) ?>',
                    icon: 'success',
                    confirmButtonColor: '#3085d6'
                });
                <?php unset($_SESSION['mensaje']); ?>
            <?php endif; ?>
            <?php if(isset($_SESSION['error'])): ?>
                Swal.fire({
                    title: 'Error',
                    text: '<?= addslashes($_SESSION['error']) ?>',
                    icon: 'error',
                    confirmButtonColor: '#d33'
                });
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
        });

        // Función para la confirmación de eliminar
        function confirmarDesactivacion(id) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "La actividad se eliminara.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: '¡Sí, eliminar!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '../../../controllers/admin/actividad/actividad_controller.php';
                    
                    const hiddenFieldAccion = document.createElement('input');
                    hiddenFieldAccion.type = 'hidden';
                    hiddenFieldAccion.name = 'accion';
                    hiddenFieldAccion.value = 'eliminar';
                    form.appendChild(hiddenFieldAccion);

                    const hiddenFieldId = document.createElement('input');
                    hiddenFieldId.type = 'hidden';
                    hiddenFieldId.name = 'id_actividad';
                    hiddenFieldId.value = id;
                    form.appendChild(hiddenFieldId);

                    document.body.appendChild(form);
                    form.submit();
                }
            })
        }
    </script>
    <script src="../js_admin/buscar_actividad_admin.js"></script>
<script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    <script src="../js_admin/admin_scripts.js"></script>
    <a href="form_actividades.php" class="floating-add-button" title="Crear Nueva Actividad"><i class="fas fa-plus"></i></a>
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
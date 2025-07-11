<?php
require_once __DIR__ . '/../../../controllers/auth/verificar_sesion.php';
require_once __DIR__ . '/../../../models/clases/actividad.php';
verificarAcceso(['Administrador']);

// Capturar los filtros de la URL
$busqueda_inicial = $_GET['busqueda'] ?? '';
$estado_inicial = $_GET['estado'] ?? '';
$modelo_actividad = new Actividad();

// Pasar los filtros al método de consulta
$actividades = $modelo_actividad->consultar($busqueda_inicial, $estado_inicial);
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
    <style>
        .admin-header { background-color: #fff; padding: 1rem 2rem; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e9ecef; }
        .search-container form { display: flex; gap: 15px; }
        .search-container input, .search-container select { padding: 12px; border: 1px solid #ccc; border-radius: 5px; font-size: 1rem; outline: none; }
        .search-container input { flex-grow: 1; }
        .search-container select { background-color: #f8f9fa; }

        .btn-report {
            background-color: #007bff; /* Color azul para reportes/información */
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 500;
            transition: background-color 0.3s, transform 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-report:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
        }
         /* Estilos generales (los mismos de tu admin_pacientes.css) */
       body {  font-family: 'Sans-serif', sans-serif; background-color: #f4f7f9; margin: 0; color: #333; }
         .admin-header { background-color: #fff; padding: 1rem 2rem; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e9ecef; }
        .logo-container { display: flex; align-items: center; }
        .logo { width: 40px; margin-right: 10px; cursor: pointer; }
        .app-name { font-size: 1.2rem; font-weight: 600; color: #2c3e50; }
        nav ul { list-style: none; margin: 0; padding: 0; display: flex; gap: 0.5rem; }
        nav ul li a { text-decoration: none; color: #555; font-weight: 500; padding: 0.5rem 1rem; border-radius: 8px; transition: all 0.2s ease; }
        nav ul li a.active, nav ul li a:hover { color: #007bff; background-color: #e6f2ff; }
        nav ul li a i { margin-right: 0.5rem; }
        .add-button-container { display: flex; gap: 1rem; }
        .btn-report, .btn-add {
            color: white; border: none; padding: 0.7rem 1.2rem; border-radius: 8px;
            cursor: pointer; font-size: 0.95rem; text-decoration: none;
            display: flex; align-items: center; gap: 0.5rem;
        }
        .btn-report { background-color: #17a2b8; }
        .btn-add { background-color: #007bff; }
        .admin-content { padding: 2.5rem; }
        .historias-container { max-width: 1200px; margin: 0 auto; }
        .historias-container h1 { font-size: 2.2rem; font-weight: 700; color: #1b263b; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 1rem; }
        
        /* Contenedor del buscador y la tabla */
        .search-container { background-color: #fff; padding: 1.5rem; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); margin-bottom: 2rem; }
        .table-container { background-color: #fff; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); overflow: hidden; }

        /* Buscador */
        .search-container form { display: flex; border: 1px solid #ced4da; border-radius: 8px; overflow: hidden; }
        .search-container select { background-color: #f8f9fa; border: none; border-right: 1px solid #ced4da; padding: 0.75rem 1rem; font-size: 1rem; outline: none; }
        .search-container input { border: none; padding: 0.75rem 1rem; font-size: 1rem; outline: none; flex-grow: 1; }
        .search-container button { border: none; background: #007bff; color: white; padding: 0 1.5rem; cursor: pointer; }
        
        /* Tabla */
        table { width: 100%; border-collapse: collapse; }
        table th, table td { padding: 1rem 1.5rem; text-align: left; border-bottom: 1px solid #f1f1f1; }
        table thead th { background-color: #007bff; color: white; font-size: 0.8rem; font-weight: 600; text-transform: uppercase; }
        table tbody tr:last-child td { border-bottom: none; }
        table tbody tr:hover { background-color: #f8f9fa; }
        
        /* Acciones y etiquetas */
        .actions { text-align: right !important; }
        .actions a, .actions button { color: #6c757d; font-size: 1.2rem; margin-left: 1rem; background: none; border: none; cursor: pointer; }
        .rol-tag { padding: 4px 10px; border-radius: 15px; color: white; font-size: 0.8em; font-weight: bold; }
        .rol-paciente { background-color: #198754; }
        .rol-cuidador { background-color: #ffc107; color: black; }
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
                <a href="admin_pacientes.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'admin_pacientes.php' ? 'active' : ''; ?>"><i class="fas fa-user-injured"></i> Pacientes</a>
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
        <h1><i class="fas fa-tasks"></i> Actividades Programadas</h1>
            
            <div class="search-container">
                <form id="searchForm" onsubmit="return false;">
                    <select id="filtro_estado" name="estado">
                        <option value=""> Todos los Estados </option>
                        <option value="Pendiente" <?= $estado_inicial == 'Pendiente' ? 'selected' : '' ?>>Pendientes</option>
                        <option value="Completada" <?= $estado_inicial == 'Completada' ? 'selected' : '' ?>>Completadas</option>
                    </select>
                    <input id="termino_busqueda" type="search" name="busqueda" placeholder="Buscar por paciente, documento o tipo de actividad..." value="<?= htmlspecialchars($busqueda_inicial) ?>">
                </form>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Actividad</th>
                            <th>Paciente</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($actividades)): ?>
                            <tr><td colspan="5">No se encontraron actividades.</td></tr>
                        <?php else: ?>
                            <?php foreach ($actividades as $actividad): ?>
                                <tr>
                                    <td><?= htmlspecialchars($actividad['tipo_actividad']) ?></td>
                                    <td><?= htmlspecialchars($actividad['nombre_paciente']) ?></td>
                                    <td><?= htmlspecialchars(date("d/m/Y", strtotime($actividad['fecha_actividad']))) ?></td>
                                    <td><?= htmlspecialchars($actividad['estado_actividad']) ?></td>
                                    <td class="actions">
                                        <?php if ($actividad['estado_actividad'] == 'Pendiente'): ?>
                                            <a href="form_actividades.php?id=<?= $actividad['id_actividad'] ?>" class="btn-action btn-edit" title="Editar"><i class="fas fa-edit"></i></a>
                                        <?php endif; ?>
                                        
                                        <button class="btn-action btn-delete" 
                                                onclick="confirmarDesactivacion(<?= $actividad['id_actividad'] ?>)" 
                                                title="Eliminar Actividad">
                                            <i class="fas fa-ban"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
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
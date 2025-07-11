<?php
require_once __DIR__ . '/../../../controllers/auth/verificar_sesion.php';
require_once __DIR__ . '/../../../models/clases/historia_clinica.php';

verificarAcceso(['Administrador']);

$modelo = new HistoriaClinica();

if (isset($_GET['desactivar_id'])) {
    try {
        $modelo->desactivarHistoria($_GET['desactivar_id']);
        $_SESSION['mensaje'] = "Historia clínica desactivada.";
    } catch (Exception $e) {
        $_SESSION['error'] = "Error al desactivar: " . $e->getMessage();
    }
    header("Location: historia_clinica.php");
    exit();
}

$busqueda = $_GET['busqueda'] ?? '';
$historias = $modelo->consultarHistorias($busqueda);
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
    <link rel="stylesheet" href="../css_admin/admin_header.css?v=<?= time(); ?>">
    <link rel="stylesheet" href="../css_admin/admin_pacientes.css?v=<?= time(); ?>">
    <link rel="stylesheet" href="../css_admin/admin_main.css?v=<?= time(); ?>">
    <style>
        /* Estilos generales (los mismos de tu admin_pacientes.css) */
        body {  font-family: 'Sans-serif', sans-serif; background-color: #f4f7f9; margin: 0; color: #333; }
        .admin-header { background-color: #fff; padding: 1rem 2rem; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e9ecef; }
        .admin-header { background-color: #fff; padding: 1rem 2rem; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e9ecef; }
        .logo-container { display: flex; align-items: center; }
        .logo { width: 40px; margin-right: 10px; cursor: pointer; }
        .app-name { font-size: 1.2rem; font-weight: 600; color: #2c3e50; }
        nav ul { list-style: none; margin: 0; padding: 0; display: flex; gap: 0.5rem; }
        nav ul li a { text-decoration: none; color: #555; font-weight: 500; padding: 0.5rem 1rem; border-radius: 8px; transition: all 0.2s ease; }
        nav ul li a.active, nav ul li a:hover { color: #007bff; background-color: #e6f2ff; }
        nav ul li a i { margin-right: 0.5rem; }
        .add-button-container .btn-add {
            background-color: #007bff; color: white; padding: 10px 20px;
            border-radius: 8px; text-decoration: none; font-weight: 500;
        }
        .admin-content { padding: 2.5rem; }
        .historias-container { max-width: 1200px; margin: 0 auto; }
        .historias-container h1 { font-size: 2.2rem; font-weight: 700; color: #1b263b; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 1rem; }
        
        /* Contenedor del buscador y la tabla */
        .search-container { background-color: #fff; padding: 1.5rem; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); margin-bottom: 2rem; }
        .table-container { background-color: #fff; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); overflow: hidden; }
        
        /* Buscador */
        .search-container form { display: flex; border: 1px solid #ced4da; border-radius: 8px; overflow: hidden; }
        .search-container input { border: none; padding: 0.75rem 1rem; font-size: 1rem; outline: none; flex-grow: 1; }
        .search-container button { border: none; background: #007bff; color: white; padding: 0 1.5rem; cursor: pointer; }
        
        /* Tabla */
        table { width: 100%; border-collapse: collapse; }
        table th, table td { padding: 1rem 1.5rem; text-align: left; border-bottom: 1px solid #f1f1f1; }
        table thead th { background-color: #007bff; color: white; font-size: 0.8rem; font-weight: 600; text-transform: uppercase; }
        table tbody tr:last-child td { border-bottom: none; }
        table tbody tr:hover { background-color: #f8f9fa; }
        
        /* Acciones de la tabla */
        .actions { text-align: right !important; }
        .actions a, .actions button { color: #6c757d; font-size: 1.2rem; margin-left: 1rem; background: none; border: none; cursor: pointer; transition: all 0.2s ease; }
        .actions a:hover { color: #007bff; transform: scale(1.2); }
        .actions button:hover { color: #dc3545; transform: scale(1.2); }
    </style>
      <link rel="stylesheet" href="../css_admin/admin_main.css?v=<?= time(); ?>">
</head>
<body>
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
            <h1><i class="fas fa-book-medical"></i> Historias Clínicas Registradas</h1>
            <div class="search-container">
                <form method="GET" action="historia_clinica.php">
                    <input type="search" name="busqueda" placeholder="Buscar por nombre o documento del paciente..." value="<?= htmlspecialchars($busqueda) ?>">
                    <button type="submit"><i class="fas fa-search"></i></button>
                </form>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Paciente</th>
                            <th>Última Consulta</th>
                            <th>Estado General</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($historias)): ?>
                            <tr><td colspan="5">No se encontraron historias clínicas.</td></tr>
                        <?php else: ?>
                            <?php foreach ($historias as $historia):
                                // Lógica para el botón inteligente
                                $estaCompleta = ($historia['med_count'] > 0 || $historia['enf_count'] > 0);
                                $claseBoton = $estaCompleta ? 'btn-primary' : 'btn-warning';
                                $textoBoton = $estaCompleta ? 'Gestionar' : 'Completar';
                                $iconoBoton = $estaCompleta ? 'fa-cog' : 'fa-exclamation-circle';
                            ?>
                                <tr>
                                    <td><?= htmlspecialchars($historia['id_historia_clinica']) ?></td>
                                    <td><?= htmlspecialchars($historia['paciente_nombre_completo']) ?></td>
                                    <td><?= htmlspecialchars($historia['fecha_formateada']) ?></td>
                                    <td><?= htmlspecialchars(substr($historia['estado_salud'], 0, 50)) . '...' ?></td>
                                    <td class="actions">
    <a href="reporte_hc_completo.php?id=<?= $historia['id_historia_clinica'] ?>" class="btn-action btn-info" title="Ver Reporte Completo">
        <i class="fas fa-eye"></i>
    </a>
    
    <a href="form_historia_clinica.php?id=<?= $historia['id_historia_clinica'] ?>" class="btn-action <?= $claseBoton ?>" title="<?= $textoBoton ?> Historia">
        <i class="fas <?= $iconoBoton ?>"></i>
    </a>

    <a href="javascript:void(0);" onclick="confirmarDesactivacionHC(<?= $historia['id_historia_clinica'] ?>)" class="btn-action btn-danger" title="Desactivar">
        <i class="fas fa-trash-alt"></i>
    </a>
</td>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            <?php if(isset($_SESSION['mensaje'])): ?>
                Swal.fire({ icon: 'success', title: '¡Éxito!', text: '<?= addslashes($_SESSION['mensaje']) ?>', timer: 2500, showConfirmButton: false });
                <?php unset($_SESSION['mensaje']); ?>
            <?php endif; ?>
            <?php if(isset($_SESSION['error'])): ?>
                Swal.fire({ icon: 'error', title: 'Error', text: '<?= addslashes($_SESSION['error']) ?>' });
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
        });
        function confirmarDesactivacionHC(id) {
            Swal.fire({
                title: '¿Estás seguro?', text: "La historia clínica se marcará como inactiva.", icon: 'warning',
                showCancelButton: true, confirmButtonColor: '#d33', cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, desactivar', cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) { window.location.href = `historia_clinica.php?desactivar_id=${id}`; }
            });
        }
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
       <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    <script src="../js_admin/admin_scripts.js"></script>
    <script src="../js_admin/admin_pacientes_copy.js" defer></script>
    <div class="add-button-container">

</div>
<a href="form_historia_clinica.php" class="floating-add-button" title="Crear Nueva Historia Clínica"><i class="fas fa-plus"></i></a>
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
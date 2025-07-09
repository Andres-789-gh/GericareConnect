<?php
require_once __DIR__ . '/../../../controllers/auth/verificar_sesion.php';
require_once __DIR__ . '/../../../models/clases/historia_clinica.php';

verificarAcceso(['Administrador']);

$modelo = new HistoriaClinica();

// Lógica para desactivar
if(isset($_GET['desactivar_id'])){
    try {
        $modelo->desactivarHistoria($_GET['desactivar_id']);
        $_SESSION['mensaje'] = "Historia clínica desactivada correctamente.";
    } catch(Exception $e) {
        $_SESSION['error'] = "Error al desactivar la historia clínica: " . $e->getMessage();
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
    <title>Gestión de Historias Clínicas - GeriCare Connect</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link rel="stylesheet" href="../libs/bootstrap/css/bootstrap.min.css">
    
    <link rel="stylesheet" href="../css_admin/admin_main.css?v=<?= time(); ?>">
    <link rel="stylesheet" href="../css_admin/historia_clinica.css?v=<?= time(); ?>">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

    <header class="header">
        <div id="particles-js"></div>
        <div class="header-content animate__animated animate__fadeIn">
            <div class="logo" onclick="window.location.href='admin_pacientes.php'">
                <img src="../../imagenes/Geri_Logo-..png" alt="Logo GeriCare" class="logo-img">
                <h1>GeriCareConnect</h1>
            </div>
            <nav class="main-nav">
                <a href="admin_pacientes.php"><i class="fas fa-user-injured"></i> Pacientes</a>
                <a href="historia_clinica.php" class="active"><i class="fas fa-notes-medical"></i> Historias Clínicas</a>
                <a href="admin_actividades.php"><i class="fas fa-calendar-alt"></i> Actividades</a>
            </nav>
            <div class="user-actions">
                <div class="user-info">
                    <div class="user-details">
                        <span class="user-name"><?= htmlspecialchars($_SESSION['nombre_usuario'] ?? 'Usuario') ?></span>
                        <span class="user-role"><?= htmlspecialchars($_SESSION['nombre_rol'] ?? 'Desconocido') ?></span>
                    </div>
                    <i class="fas fa-user-circle user-avatar"></i>
                </div>
            </div>
        </div>
    </header>

    <main class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
             <h1 class="mb-0"><i class="fas fa-file-medical"></i> Gestión de Historias Clínicas</h1>
             <a href="form_historia_clinica.php" class="btn-main-action">
                <i class="fas fa-plus"></i> Crear Historia Clínica
            </a>
        </div>
        
        <div class="search-container">
            <form method="GET" action="historia_clinica.php">
                <div class="search-input-group">
                    <i class="fas fa-search search-icon"></i>
                    <input type="search" name="busqueda" class="form-control" placeholder="Buscar por nombre o documento del paciente..." value="<?= htmlspecialchars($busqueda) ?>">
                </div>
            </form>
        </div>

        <div class="table-responsive mt-4">
            <table class="table-custom">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Paciente</th>
                        <th>Última Consulta</th>
                        <th>Estado General (resumen)</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($historias)): ?>
                        <tr><td colspan="5" class="text-center py-5">No se encontraron historias clínicas que coincidan con la búsqueda.</td></tr>
                    <?php else: foreach($historias as $historia):
                        $estaCompleta = ($historia['med_count'] > 0 || $historia['enf_count'] > 0);
                        $claseIcono = $estaCompleta ? 'fa-cog' : 'fa-exclamation-circle';
                        $claseColor = $estaCompleta ? 'btn-gestion' : 'btn-alerta';
                        $titulo = $estaCompleta ? 'Gestionar Historia (Completa)' : 'Gestionar Historia (Incompleta)';
                    ?>
                        <tr class="animate__animated animate__fadeIn">
                            <td><b><?= htmlspecialchars($historia['id_historia_clinica']) ?></b></td>
                            <td><?= htmlspecialchars($historia['paciente_nombre_completo']) ?></td>
                            <td><?= htmlspecialchars($historia['fecha_formateada']) ?></td>
                            <td><small class="text-muted"><?= htmlspecialchars(substr($historia['estado_salud'], 0, 70)) . '...' ?></small></td>
                            <td class="actions text-center">
                                <a href="reporte_hc_completo.php?id=<?= $historia['id_historia_clinica'] ?>" class="btn-action btn-view" title="Ver Reporte Completo"><i class="fas fa-eye"></i></a>
                                <a href="form_historia_clinica.php?id=<?= $historia['id_historia_clinica'] ?>" class="btn-action <?= $claseColor ?>" title="<?= $titulo ?>"><i class="fas <?= $claseIcono ?>"></i></a>
                                <button onclick="confirmarDesactivacion(<?= $historia['id_historia_clinica'] ?>)" class="btn-action btn-delete" title="Desactivar Historia"><i class="fas fa-trash-alt"></i></button>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    <script src="../js_admin/admin_scripts.js"></script>
    <script>
    // Tu función de SweetAlert para confirmar la desactivación
    function confirmarDesactivacion(id) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "Esta acción desactivará la historia clínica, pero no la eliminará permanentemente.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, ¡desactivar!',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `historia_clinica.php?desactivar_id=${id}`;
            }
        });
    }

    // Alertas de éxito o error desde PHP
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
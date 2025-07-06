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
    <title>Gestión de Historias Clínicas</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="stylesheet" href="../css_admin/historia_clinica_lista.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <header class="admin-header">
        <!-- ... (tu header se mantiene igual) ... -->
        <div class="logo-container">
            <img src="../../imagenes/Geri_Logo-..png" alt="Logo" class="logo" onclick="window.location.href='admin_pacientes.php'">
            <span class="app-name">GERICARE CONNECT</span>
        </div>
        <nav>
            <ul>
                <li><a href="admin_pacientes.php"><i class="fas fa-user-injured"></i> Pacientes</a></li>
                <li><a href="historia_clinica.php" class="active"><i class="fas fa-file-medical"></i> Historias Clínicas</a></li>
                <li><a href="../../../controllers/admin/logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a></li>
            </ul>
        </nav>
        <div class="add-button-container">
            <a href="form_historia_clinica.php" class="btn-add"><i class="fas fa-plus"></i> Crear Historia Clínica</a>
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
                                        <a href="form_historia_clinica.php?id=<?= $historia['id_historia_clinica'] ?>" class="btn-action <?= $claseBoton ?>" title="Gestionar Historia">
                                            <i class="fas <?= $iconoBoton ?>"></i>
                                        </a>
                                        <a href="javascript:void(0);" onclick="confirmarDesactivacion(<?= $historia['id_historia_clinica'] ?>)" class="btn-action btn-danger" title="Desactivar">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
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
            <?php if(isset($_SESSION['mensaje'])): ?>
                Swal.fire({ icon: 'success', title: '¡Éxito!', text: '<?= addslashes($_SESSION['mensaje']) ?>', timer: 2500, showConfirmButton: false });
                <?php unset($_SESSION['mensaje']); ?>
            <?php endif; ?>
            <?php if(isset($_SESSION['error'])): ?>
                Swal.fire({ icon: 'error', title: 'Error', text: '<?= addslashes($_SESSION['error']) ?>' });
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
        });
        function confirmarDesactivacion(id) {
            Swal.fire({
                title: '¿Estás seguro?', text: "La historia clínica se marcará como inactiva.", icon: 'warning',
                showCancelButton: true, confirmButtonColor: '#d33', cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, desactivar', cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) { window.location.href = `historia_clinica.php?desactivar_id=${id}`; }
            });
        }
    </script>
</body>
</html>
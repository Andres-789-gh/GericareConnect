<?php
require_once __DIR__ . '/../../../controllers/auth/verificar_sesion.php';
require_once __DIR__ . '/../../../models/clases/historia_clinica.php';
verificarAcceso(['Administrador']);
$modelo = new HistoriaClinica();
if(isset($_GET['desactivar_id'])){ try{ $modelo->desactivarHistoria($_GET['desactivar_id']); $_SESSION['mensaje']="Historia clínica desactivada."; }catch(Exception $e){ $_SESSION['error']="Error al desactivar: ".$e->getMessage(); } header("Location: historia_clinica.php"); exit(); }
$busqueda = $_GET['busqueda'] ?? '';
$historias = $modelo->consultarHistorias($busqueda);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"><title>Gestión de Historias Clínicas</title>
    <link rel="stylesheet" href="../css_admin/admin_main.css?v=<?= time(); ?>">
    <link rel="stylesheet" href="../libs/bootstrap/css/bootstrap.min.css"><link rel="stylesheet" href="../libs/animate/animate.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"><script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <header class="admin-header">
        <div class="logo-container"><img src="../../imagenes/Geri_Logo-..png" alt="Logo" class="logo"><span class="app-name">GERICARE CONNECT</span></div>
        <nav>
            <ul>
                <li><a href="admin_pacientes.php"><i class="fas fa-users"></i> Usuarios</a></li>
                <li><a href="historia_clinica.php" class="active"><i class="fas fa-file-medical"></i> Historias Clínicas</a></li>
                <li><a href="admin_actividades.php"><i class="fas fa-tasks"></i> Actividades</a></li>
                <li><a href="../../../controllers/admin/logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a></li>
            </ul>
        </nav>
        <div class="add-button-container"><a href="form_historia_clinica.php" class="btn-header btn-add-historia"><i class="fas fa-plus"></i> Crear Historia</a></div>
    </header>
    <main class="main-content">
        <div class="content-container">
            <h1 class="animate__animated animate__fadeInDown">Gestión de Historias Clínicas</h1>
            <div class="card search-card animate__animated animate__fadeInUp">
                <form method="GET" action="historia_clinica.php">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0 text-muted"><i class="fas fa-search"></i></span>
                        <input type="search" name="busqueda" class="form-control border-start-0" placeholder="Buscar por nombre o documento del paciente..." value="<?= htmlspecialchars($busqueda) ?>">
                    </div>
                </form>
            </div>
            <div class="table-container animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">
                <table>
                    <thead><tr><th>ID</th><th>Paciente</th><th>Última Consulta</th><th>Estado General</th><th class="text-end">Acciones</th></tr></thead>
                    <tbody>
                        <?php if(empty($historias)): ?>
                            <tr><td colspan="5" class="text-center text-muted py-5">No se encontraron historias clínicas.</td></tr>
                        <?php else: foreach($historias as $historia):
                            $estaCompleta = ($historia['med_count'] > 0 || $historia['enf_count'] > 0);
                            $claseIcono = $estaCompleta ? 'fa-cog' : 'fa-exclamation-circle';
                            $claseColor = $estaCompleta ? 'action-gestion' : 'action-edit text-warning';
                        ?>
                            <tr class="animate-row">
                                <td><b><?= $historia['id_historia_clinica'] ?></b></td>
                                <td><?= $historia['paciente_nombre_completo'] ?></td>
                                <td><?= $historia['fecha_formateada'] ?></td>
                                <td><small class="text-muted"><?= htmlspecialchars(substr($historia['estado_salud'], 0, 50)) . '...' ?></small></td>
                                <td class="actions">
                                    <div class="actions-group">
                                        <a href="reporte_hc_completo.php?id=<?= $historia['id_historia_clinica'] ?>" class="action-view" title="Ver Reporte"><i class="fas fa-eye"></i></a>
                                        <a href="form_historia_clinica.php?id=<?= $historia['id_historia_clinica'] ?>" class="<?= $claseColor ?>" title="Gestionar"><i class="fas <?= $claseIcono ?>"></i></a>
                                        <button onclick="confirmarDesactivacion(<?= $historia['id_historia_clinica'] ?>)" class="action-delete" title="Desactivar"><i class="fas fa-trash-alt"></i></button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
    <script src="../js_admin/admin_scripts.js"></script>
    <script> /* JS para SweetAlert */ </script>
</body>
</html>
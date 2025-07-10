<?php
require_once __DIR__ . '/../../../controllers/auth/verificar_sesion.php';
require_once __DIR__ . '/../../../models/clases/actividad.php';
verificarAcceso(['Administrador']);
$busqueda = $_GET['busqueda'] ?? '';
$estado_filtro = $_GET['estado'] ?? '';
$modelo_actividad = new Actividad();
$actividades = $modelo_actividad->consultar($busqueda, $estado_filtro);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"><title>Gestión de Actividades</title>
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
                <li><a href="historia_clinica.php"><i class="fas fa-file-medical"></i> Historias Clínicas</a></li>
                <li><a href="admin_actividades.php" class="active"><i class="fas fa-tasks"></i> Actividades</a></li>
                <li><a href="../../../controllers/admin/logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a></li>
            </ul>
        </nav>
        <div class="add-button-container">
            <a href="reporte_actividades_cuidador.php" class="btn-header btn-report"><i class="fas fa-chart-line"></i> Reporte</a>
            <a href="form_actividades.php" class="btn-header btn-add-actividad"><i class="fas fa-plus"></i> Nueva Actividad</a>
        </div>
    </header>
    <main class="main-content">
        <div class="content-container">
            <h1 class="animate__animated animate__fadeInDown">Actividades Programadas</h1>
            <div class="card search-card animate__animated animate__fadeInUp">
                <form method="GET">
                    <div class="input-group">
                        <select name="estado" class="form-select" style="max-width: 200px;" onchange="this.form.submit()">
                            <option value="">Todos los Estados</option>
                            <option value="Pendiente" <?= $estado_filtro == 'Pendiente' ? 'selected' : '' ?>>Pendientes</option>
                            <option value="Completada" <?= $estado_filtro == 'Completada' ? 'selected' : '' ?>>Completadas</option>
                        </select>
                        <input type="search" name="busqueda" class="form-control" placeholder="Buscar por paciente o actividad..." value="<?= htmlspecialchars($busqueda) ?>">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
                    </div>
                </form>
            </div>
            <div class="table-container animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">
                <table>
                    <thead><tr><th>Actividad</th><th>Paciente</th><th>Fecha</th><th>Estado</th><th class="text-end">Acciones</th></tr></thead>
                    <tbody>
                        <?php if(empty($actividades)): ?>
                            <tr><td colspan="5" class="text-center text-muted py-5">No se encontraron actividades.</td></tr>
                        <?php else: foreach($actividades as $actividad): ?>
                            <tr class="animate-row">
                                <td><?= $actividad['tipo_actividad'] ?></td>
                                <td><?= $actividad['nombre_paciente'] ?></td>
                                <td><?= date("d/m/Y", strtotime($actividad['fecha_actividad'])) ?></td>
                                <td><span class="rol-tag <?= $actividad['estado_actividad'] == 'Pendiente' ? 'rol-cuidador' : 'rol-paciente' ?>"><?= $actividad['estado_actividad'] ?></span></td>
                                <td class="actions">
                                    <div class="actions-group">
                                        <?php if ($actividad['estado_actividad'] == 'Pendiente'): ?>
                                            <a href="form_actividades.php?id=<?= $actividad['id_actividad'] ?>" class="action-edit" title="Editar"><i class="fas fa-edit"></i></a>
                                        <?php endif; ?>
                                        <button onclick="confirmarDesactivacion(<?= $actividad['id_actividad'] ?>)" class="action-ban" title="Eliminar"><i class="fas fa-ban"></i></button>
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
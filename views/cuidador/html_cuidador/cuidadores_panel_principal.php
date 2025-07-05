<?php
session_start();
if (!isset($_SESSION['id_usuario']) || $_SESSION['nombre_rol'] !== 'Cuidador') {
    header("Location: /GericareConnect/views/index-login/htmls/index.html");
    exit();
}

// Lógica para obtener las actividades del cuidador.
require_once __DIR__ . '/../../../models/clases/actividad.php';
$actividad_model = new Actividad();
$lista_actividades = $actividad_model->consultarPorCuidador($_SESSION['id_usuario']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel del Cuidador - GeriCare Connect</title>
    </head>
<body>
    <header class="main-header">
        </header>

    <main class="admin-content">
        <div class="pacientes-container">
            <h1><i class="fas fa-calendar-alt"></i> Mis Actividades Programadas</h1>
            <div class="toolbar">
                <a href="form_actividad.php" class="add-paciente-button"><i class="fas fa-plus-circle"></i> Agregar Nueva Actividad</a>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Actividad</th>
                            <th>Paciente Asignado</th>
                            <th>Fecha</th>
                            <th>Hora</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($lista_actividades)): ?>
                            <tr><td colspan="5" style="text-align:center;">No tienes actividades programadas.</td></tr>
                        <?php else: ?>
                            <?php foreach ($lista_actividades as $actividad): ?>
                                <tr>
                                    <td><?= htmlspecialchars($actividad['tipo_actividad']) ?></td>
                                    <td><?= htmlspecialchars($actividad['nombre_paciente'] . ' ' . $actividad['apellido_paciente']) ?></td>
                                    <td><?= htmlspecialchars($actividad['fecha_actividad']) ?></td>
                                    <td><?= htmlspecialchars($actividad['hora_inicio']) ?></td>
                                    <td><span class="estado-pendiente"><?= htmlspecialchars($actividad['estado_actividad']) ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Script para mostrar notificaciones de éxito o error.
        document.addEventListener('DOMContentLoaded', function() {
            <?php if(isset($_SESSION['mensaje'])): ?>
                Swal.fire({ icon: 'success', title: '¡Éxito!', text: '<?= addslashes($_SESSION['mensaje']) ?>', timer: 2500, showConfirmButton: false });
                <?php unset($_SESSION['mensaje']); ?>
            <?php endif; ?>
        });
    </script>
</body>
</html>

<?php
session_start();
if (!isset($_SESSION['id_usuario']) || $_SESSION['nombre_rol'] !== 'Cuidador') {
    header("Location: /GericareConnect/views/index-login/htmls/index.php");
    exit();
}

// Necesitamos la lista de todos los pacientes para el menú desplegable.
require_once __DIR__ . '/../../../models/clases/pacientes.php';
$paciente_model = new Paciente();
$lista_pacientes = $paciente_model->consultar();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Actividad</title>
    </head>
<body>
    <div class="container">
        <h1><i class="fas fa-calendar-plus"></i> Registrar Nueva Actividad</h1>
        
        <form action="../../../controllers/cuidador/actividad_controller.php" method="POST">
            <input type="hidden" name="accion" value="registrar">
            <div class="form-grid">
                <div class="form-group">
                    <label>Tipo de Actividad</label>
                    <input type="text" name="tipo_actividad" placeholder="Ej: Paseo matutino, Terapia física..." required>
                </div>
                <div class="form-group">
                    <label>Asignar a Paciente</label>
                    <select name="id_paciente" required>
                        <option value="">Seleccione un paciente...</option>
                        <?php foreach ($lista_pacientes as $paciente): ?>
                            <option value="<?= $paciente['id_paciente'] ?>"><?= htmlspecialchars($paciente['nombre'] . ' ' . $paciente['apellido']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Fecha de la Actividad</label>
                    <input type="date" name="fecha_actividad" required>
                </div>
                <div class="form-group">
                    <label>Hora de Inicio</label>
                    <input type="time" name="hora_inicio">
                </div>
                <div class="form-group full-width">
                    <label>Descripción Adicional</label>
                    <textarea name="descripcion_actividad" rows="4"></textarea>
                </div>
            </div>
            <div class="toolbar">
                <a href="cuidadores_panel_principal.php" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar Actividad</button>
            </div>
        </form>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Script para mostrar el mensaje de error si el controlador lo envía.
        document.addEventListener('DOMContentLoaded', function() {
            <?php if(isset($_SESSION['error'])): ?>
                Swal.fire({ icon: 'error', title: 'Error al Guardar', text: '<?= addslashes($_SESSION['error']) ?>' });
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
        });
    </script>
</body>
</html>

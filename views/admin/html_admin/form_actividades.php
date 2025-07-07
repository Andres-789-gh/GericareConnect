<?php
require_once __DIR__ . '/../../../controllers/auth/verificar_sesion.php';
require_once __DIR__ . '/../../../models/clases/actividad.php';
require_once __DIR__ . '/../../../models/clases/pacientes.php';
verificarAcceso(['Administrador']);

$modelo_actividad = new Actividad();
$modelo_paciente = new Paciente();

$modo_edicion = false;
$actividad = [];
$pacientes = $modelo_paciente->consultar(); // Obtener lista de pacientes activos

if (isset($_GET['id'])) {
    $modo_edicion = true;
    $actividad = $modelo_actividad->obtenerPorId($_GET['id']);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= $modo_edicion ? 'Editar' : 'Agregar' ?> Actividad</title>
    <link rel="stylesheet" href="../css_admin/historia_clinica_form.css"> </head>
<body>
    <div class="form-container">
        <h1><i class="fas fa-calendar-plus"></i> <?= $modo_edicion ? 'Editar Actividad' : 'Nueva Actividad' ?></h1>
        <form action="../../../controllers/admin/actividad/actividad_controller.php" method="POST">
            <input type="hidden" name="accion" value="<?= $modo_edicion ? 'actualizar' : 'registrar' ?>">
            <?php if ($modo_edicion) echo "<input type='hidden' name='id_actividad' value='{$actividad['id_actividad']}'>"; ?>

            <div class="form-grid">
                <div class="form-group full-width">
                    <label for="id_paciente">Paciente</label>
                    <select name="id_paciente" required>
                        <option value="">-- Seleccione un paciente --</option>
                        <?php foreach ($pacientes as $paciente): ?>
                            <option value="<?= $paciente['id_paciente'] ?>" <?= ($modo_edicion && $actividad['id_paciente'] == $paciente['id_paciente']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($paciente['nombre'] . ' ' . $paciente['apellido']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group"><label>Tipo de Actividad</label><input type="text" name="tipo_actividad" value="<?= htmlspecialchars($actividad['tipo_actividad'] ?? '') ?>" required></div>
                <div class="form-group"><label>Fecha</label><input type="date" name="fecha_actividad" value="<?= htmlspecialchars($actividad['fecha_actividad'] ?? '') ?>" required></div>
                <div class="form-group"><label>Hora Inicio</label><input type="time" name="hora_inicio" value="<?= htmlspecialchars($actividad['hora_inicio'] ?? '') ?>"></div>
                <div class="form-group"><label>Hora Fin</label><input type="time" name="hora_fin" value="<?= htmlspecialchars($actividad['hora_fin'] ?? '') ?>"></div>
                
                <div class="form-group full-width"><label>Descripción</label><textarea name="descripcion_actividad" rows="4"><?= htmlspecialchars($actividad['descripcion_actividad'] ?? '') ?></textarea></div>
            </div>

            <div class="form-actions">
                <a href="admin_actividades.php" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar</button>
            </div>
        </form>
    </div>
</body>
</html>
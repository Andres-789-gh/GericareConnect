<?php
require_once __DIR__ . '/../../../controllers/auth/verificar_sesion.php';
require_once __DIR__ . '/../../../models/clases/historia_clinica.php';
require_once __DIR__ . '/../../../models/clases/pacientes.php'; // Para obtener la lista de pacientes

verificarAcceso(['Administrador']);

$modelo_hc = new HistoriaClinica();
$modelo_paciente = new Paciente();

$modo_edicion = false;
$datos_hc = [];

// Obtener la lista de pacientes activos para el menú desplegable
$pacientes = $modelo_paciente->consultar();

// Si se recibe un ID, estamos en modo edición
if (isset($_GET['id'])) {
    $modo_edicion = true;
    $datos_hc = $modelo_hc->obtenerHistoriaPorId($_GET['id']);
    if (!$datos_hc) {
        // Si no se encuentra la historia, redirigir con un error
        $_SESSION['error'] = "No se encontró la historia clínica solicitada.";
        header("Location: historia_clinica.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $modo_edicion ? 'Editar' : 'Crear' ?> Historia Clínica</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="stylesheet" href="../css_admin/historia_clinica_form.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="form-container">
        <h1><i class="fas fa-file-signature"></i> <?= $modo_edicion ? 'Editar' : 'Nueva' ?> Historia Clínica</h1>

        <form action="../../../controllers/admin/HC/historia_clinica_controller.php" method="POST">
            <!-- Campos ocultos para la acción y el ID en modo edición -->
            <input type="hidden" name="accion" value="<?= $modo_edicion ? 'actualizar' : 'registrar' ?>">
            <?php if ($modo_edicion): ?>
                <input type="hidden" name="id_historia_clinica" value="<?= htmlspecialchars($datos_hc['id_historia_clinica']) ?>">
            <?php endif; ?>

            <div class="form-grid">
                <!-- Selección de Paciente -->
                <div class="form-group full-width">
                    <label for="id_paciente">Paciente</label>
                    <select name="id_paciente" id="id_paciente" required <?= $modo_edicion ? 'disabled' : '' ?>>
                        <option value="">-- Seleccione un paciente --</option>
                        <?php foreach ($pacientes as $paciente): ?>
                            <option value="<?= $paciente['id_paciente'] ?>" <?= (isset($datos_hc['id_paciente']) && $datos_hc['id_paciente'] == $paciente['id_paciente']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($paciente['nombre'] . ' ' . $paciente['apellido']) ?> (Doc: <?= htmlspecialchars($paciente['documento_identificacion']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if ($modo_edicion): ?>
                        <!-- Si estamos editando, el paciente no se puede cambiar. Enviamos el ID en un campo oculto. -->
                        <input type="hidden" name="id_paciente" value="<?= htmlspecialchars($datos_hc['id_paciente']) ?>">
                    <?php endif; ?>
                </div>

                <!-- Campos de texto -->
                <div class="form-group full-width">
                    <label for="estado_salud">Estado de Salud General</label>
                    <textarea name="estado_salud" id="estado_salud" rows="4" required><?= htmlspecialchars($datos_hc['estado_salud'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label for="condiciones">Condiciones Médicas Preexistentes</label>
                    <textarea name="condiciones" id="condiciones" rows="3"><?= htmlspecialchars($datos_hc['condiciones'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label for="antecedentes_medicos">Antecedentes Médicos Familiares</label>
                    <textarea name="antecedentes_medicos" id="antecedentes_medicos" rows="3"><?= htmlspecialchars($datos_hc['antecedentes_medicos'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label for="alergias">Alergias Conocidas</label>
                    <textarea name="alergias" id="alergias" rows="3"><?= htmlspecialchars($datos_hc['alergias'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label for="dietas_especiales">Dietas Especiales</label>
                    <textarea name="dietas_especiales" id="dietas_especiales" rows="3"><?= htmlspecialchars($datos_hc['dietas_especiales'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label for="fecha_ultima_consulta">Fecha de Última Consulta</label>
                    <input type="date" name="fecha_ultima_consulta" id="fecha_ultima_consulta" value="<?= htmlspecialchars($datos_hc['fecha_ultima_consulta'] ?? '') ?>" required>
                </div>
                <div class="form-group full-width">
                    <label for="observaciones">Observaciones Adicionales</label>
                    <textarea name="observaciones" id="observaciones" rows="4"><?= htmlspecialchars($datos_hc['observaciones'] ?? '') ?></textarea>
                </div>
            </div>

            <div class="form-actions">
                <a href="historia_clinica.php" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> <?= $modo_edicion ? 'Actualizar Historia' : 'Guardar Historia' ?>
                </button>
            </div>
        </form>
    </div>
    <script>
        // Script para mostrar errores que puedan venir del controlador
        document.addEventListener('DOMContentLoaded', function() {
            <?php if(isset($_SESSION['error'])): ?>
                Swal.fire({ icon: 'error', title: 'Error', text: '<?= addslashes($_SESSION['error']) ?>' });
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
        });
    </script>
</body>
</html>
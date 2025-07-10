<?php
session_start();
// Seguridad y carga de clases
if (!isset($_SESSION['id_usuario']) || $_SESSION['nombre_rol'] !== 'Administrador') {
    header("Location: /GericareConnect/views/index-login/htmls/index.php");
    exit();
}
require_once __DIR__ . '/../../../models/clases/pacientes.php';
require_once __DIR__ . '/../../../models/clases/usuario.php';

$user_model = new usuario();
$lista_familiares = $user_model->obtenerUsuariosPorRol('Familiar');
$lista_cuidadores = $user_model->obtenerUsuariosPorRol('Cuidador');

$modo_edicion = false;
$datos_paciente = [];
$asignacion_activa = null; 

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $modo_edicion = true;
    $pacienteModel = new Paciente();
    $datos_paciente = $pacienteModel->obtenerPorId($_GET['id']);
    $asignacion_activa = $pacienteModel->obtenerAsignacionActiva($_GET['id']);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= $modo_edicion ? 'Editar' : 'Agregar' ?> Paciente</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root { --primary-color: #007bff; --light-gray: #f8f9fa; --medium-gray: #dee2e6; --dark-gray: #6c757d; --text-color: #212529; --white: #ffffff; --shadow: 0 4px 15px rgba(0, 0, 0, 0.08); --success-color: #28a745; }
        body { font-family: 'Poppins', sans-serif; background-color: var(--light-gray); margin: 0; padding: 2rem; }
        .container { max-width: 950px; margin: 0 auto; background: var(--white); padding: 2rem 3rem; border-radius: 12px; box-shadow: var(--shadow); border-top: 5px solid var(--primary-color); }
        h1 { font-size: 1.8rem; margin-bottom: 2rem; display: flex; align-items: center; gap: 12px; color: var(--primary-color); }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem 2rem; }
        .form-group label { margin-bottom: 8px; font-weight: 500; color: var(--dark-gray); font-size: 0.9rem; }
        input, select, textarea { width: 100%; padding: 12px 15px; border: 1px solid var(--medium-gray); border-radius: 8px; font-size: 1rem; box-sizing: border-box; }
        input:disabled, select:disabled { background-color: #e9ecef; cursor: not-allowed; }
        textarea { resize: vertical; min-height: 80px; }
        .full-width { grid-column: 1 / -1; }
        .toolbar { margin-top: 2rem; display: flex; justify-content: flex-end; gap: 1rem; }
        .btn { padding: 12px 25px; border: none; border-radius: 8px; font-size: 1rem; font-weight: 600; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; }
        .btn-primary { background: var(--success-color); color: var(--white); }
        .btn-secondary { background-color: var(--dark-gray); color: var(--white); }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-user-plus"></i> <?= $modo_edicion ? 'Editar Paciente' : 'Registrar Paciente' ?></h1>
        <form action="../../../controllers/admin/paciente_controller.php" method="POST">
            <input type="hidden" name="accion" value="<?= $modo_edicion ? 'actualizar' : 'registrar' ?>">
            <?php if ($modo_edicion): ?>
                <input type="hidden" name="id_paciente" value="<?= htmlspecialchars($datos_paciente['id_paciente']) ?>">
                <input type="hidden" name="nombre" value="<?= htmlspecialchars($datos_paciente['nombre']) ?>">
                <input type="hidden" name="apellido" value="<?= htmlspecialchars($datos_paciente['apellido']) ?>">
                <input type="hidden" name="documento_identificacion" value="<?= htmlspecialchars($datos_paciente['documento_identificacion']) ?>">
                <input type="hidden" name="fecha_nacimiento" value="<?= htmlspecialchars($datos_paciente['fecha_nacimiento']) ?>">
                <input type="hidden" name="tipo_sangre" value="<?= htmlspecialchars($datos_paciente['tipo_sangre']) ?>">
            <?php endif; ?>

            <div class="form-grid">
                <div class="form-group"><label>Nombres</label><input type="text" name="nombre_display" value="<?= htmlspecialchars($datos_paciente['nombre'] ?? '') ?>" required <?= $modo_edicion ? 'disabled' : '' ?>></div>
                <div class="form-group"><label>Apellidos</label><input type="text" name="apellido_display" value="<?= htmlspecialchars($datos_paciente['apellido'] ?? '') ?>" required <?= $modo_edicion ? 'disabled' : '' ?>></div>
                <div class="form-group"><label>Documento</label><input type="number" name="documento_identificacion_display" value="<?= htmlspecialchars($datos_paciente['documento_identificacion'] ?? '') ?>" required <?= $modo_edicion ? 'disabled' : '' ?>></div>
                <div class="form-group"><label>Fecha de Nacimiento</label><input type="date" name="fecha_nacimiento_display" value="<?= htmlspecialchars($datos_paciente['fecha_nacimiento'] ?? '') ?>" required <?= $modo_edicion ? 'disabled' : '' ?>></div>
                <div class="form-group"><label>Género</label><select name="genero" required><option value="">Seleccione...</option><option value="Masculino" <?= (($datos_paciente['genero'] ?? '') == 'Masculino') ? 'selected' : '' ?>>Masculino</option><option value="Femenino" <?= (($datos_paciente['genero'] ?? '') == 'Femenino') ? 'selected' : '' ?>>Femenino</option></select></div>
                <div class="form-group"><label>Contacto de Emergencia</label><input type="text" name="contacto_emergencia" value="<?= htmlspecialchars($datos_paciente['contacto_emergencia'] ?? '') ?>" required></div>
                <div class="form-group"><label>Estado Civil</label><input type="text" name="estado_civil" value="<?= htmlspecialchars($datos_paciente['estado_civil'] ?? '') ?>" required></div>
                
                <div class="form-group">
                    <label>Tipo de Sangre</label>
                    <select name="tipo_sangre_display" required <?= $modo_edicion ? 'disabled' : '' ?>>
                        <?php $tipos_sangre = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']; ?>
                        <option value="">Seleccione...</option>
                        <?php foreach ($tipos_sangre as $tipo): ?>
                            <option value="<?= $tipo ?>" <?= (isset($datos_paciente['tipo_sangre']) && $datos_paciente['tipo_sangre'] == $tipo) ? 'selected' : '' ?>><?= $tipo ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group"><label>Seguro Médico (EPS)</label><input type="text" name="seguro_medico" value="<?= htmlspecialchars($datos_paciente['seguro_medico'] ?? '') ?>"></div>
                <div class="form-group"><label>Número de Afiliación</label><input type="text" name="numero_seguro" value="<?= htmlspecialchars($datos_paciente['numero_seguro'] ?? '') ?>"></div>
                
                <div class="form-group full-width">
                    <label for="id_usuario_cuidador"><i class="fas fa-user-nurse"></i> Asignar Cuidador</label>
                    <select name="id_usuario_cuidador" id="id_usuario_cuidador" required>
                        <option value=""> Seleccione un Cuidador </option>
                        <?php foreach ($lista_cuidadores as $cuidador): ?>
                            <option value="<?= $cuidador['id_usuario'] ?>" <?= ($asignacion_activa && $asignacion_activa['id_usuario_cuidador'] == $cuidador['id_usuario']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cuidador['nombre'] . ' ' . $cuidador['apellido']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group full-width">
                    <label for="descripcion_asignacion">Descripción de la Asignación</label>
                    <textarea name="descripcion_asignacion" id="descripcion_asignacion" rows="3" placeholder="Ej: Asignado para cubrir turno. Paciente requiere supervisión constante."><?= htmlspecialchars($asignacion_activa['descripcion'] ?? '') ?></textarea>
                </div>

                <div class="form-group full-width">
                    <label for="id_usuario_familiar"><i class="fas fa-link"></i> Enlazar Familiar (Opcional)</label>
                    <select name="id_usuario_familiar" id="id_usuario_familiar">
                        <option value="">Ninguno</option>
                        <?php foreach ($lista_familiares as $familiar):?>
                            <option value="<?=$familiar['id_usuario']?>" <?= (isset($datos_paciente['id_usuario_familiar']) && $datos_paciente['id_usuario_familiar'] == $familiar['id_usuario']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($familiar['nombre'].' '.$familiar['apellido']) ?>
                            </option>
                        <?php endforeach;?>
                    </select>
                </div>
            </div>
            <div class="toolbar">
                <a href="admin_pacientes.php" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> <?= $modo_edicion ? 'Actualizar Cambios' : 'Guardar Paciente' ?></button>
            </div>
        </form>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        <?php if(isset($_SESSION['error'])): ?>
            Swal.fire({ icon: 'error', title: 'Error al Guardar', text: '<?= addslashes($_SESSION['error']) ?>' });
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
    });
    </script>
</body>
</html>
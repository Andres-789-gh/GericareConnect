<?php
session_start();
// Tu seguridad y lógica PHP
require_once __DIR__ . '/../../../models/clases/pacientes.php';
require_once __DIR__ . '/../../../models/clases/usuario.php';

// Inicialización de variables
$user_model = new usuario();
$lista_familiares = $user_model->obtenerUsuariosPorRol('Familiar');
$lista_cuidadores = $user_model->obtenerUsuariosPorRol('Cuidador');
$modo_edicion = false;
$datos_paciente = [];
$asignacion_activa = null; 

// Lógica para modo edición
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $modo_edicion ? 'Editar' : 'Registrar' ?> Paciente - GeriCare</title>
    
    <link rel="stylesheet" href="../libs/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link rel="stylesheet" href="../css_admin/admin_main.css?v=<?= time(); ?>">
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
                <a href="historia_clinica.php"><i class="fas fa-notes-medical"></i> Historias Clínicas</a>
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
        <div class="form-container-card animate__animated animate__fadeInUp">
            <h1 class="form-title-main"><?= $modo_edicion ? '<i class="fas fa-user-edit"></i> Editar Paciente' : '<i class="fas fa-user-plus"></i> Registrar Nuevo Paciente' ?></h1>
            
            <form action="../../../controllers/admin/paciente_controller.php" method="POST">
                <input type="hidden" name="accion" value="<?= $modo_edicion ? 'actualizar' : 'registrar' ?>">
                <?php if ($modo_edicion) echo "<input type='hidden' name='id_paciente' value='{$datos_paciente['id_paciente']}'>"; ?>
                
                <fieldset class="mb-4">
                    <legend><i class="fas fa-user-circle me-2"></i>Datos Personales</legend>
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label">Nombres</label><input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($datos_paciente['nombre'] ?? '') ?>" required></div>
                        <div class="col-md-6"><label class="form-label">Apellidos</label><input type="text" name="apellido" class="form-control" value="<?= htmlspecialchars($datos_paciente['apellido'] ?? '') ?>" required></div>
                        <div class="col-md-6"><label class="form-label">Documento</label><input type="number" name="documento_identificacion" class="form-control" value="<?= htmlspecialchars($datos_paciente['documento_identificacion'] ?? '') ?>" required></div>
                        <div class="col-md-6"><label class="form-label">Fecha de Nacimiento</label><input type="date" name="fecha_nacimiento" class="form-control" value="<?= htmlspecialchars($datos_paciente['fecha_nacimiento'] ?? '') ?>" required></div>
                        <div class="col-md-6"><label class="form-label">Género</label><select name="genero" class="form-select" required><option value="">Seleccione...</option><option value="Masculino" <?= (($datos_paciente['genero'] ?? '') == 'Masculino') ? 'selected' : '' ?>>Masculino</option><option value="Femenino" <?= (($datos_paciente['genero'] ?? '') == 'Femenino') ? 'selected' : '' ?>>Femenino</option></select></div>
                        <div class="col-md-6"><label class="form-label">Contacto de Emergencia</label><input type="text" name="contacto_emergencia" class="form-control" value="<?= htmlspecialchars($datos_paciente['contacto_emergencia'] ?? '') ?>" required></div>
                    </div>
                </fieldset>

                <fieldset class="mb-4">
                    <legend><i class="fas fa-heartbeat me-2"></i>Información Adicional</legend>
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label">Estado Civil</label><input type="text" name="estado_civil" class="form-control" value="<?= htmlspecialchars($datos_paciente['estado_civil'] ?? '') ?>" required></div>
                        <div class="col-md-6"><label class="form-label">Tipo de Sangre</label><select name="tipo_sangre" id="tipo_sangre" class="form-select" required><option value="">Seleccione...</option><option value="A+">A+</option><option value="A-">A-</option><option value="B+">B+</option><option value="B-">B-</option><option value="AB+">AB+</option><option value="AB-">AB-</option><option value="O+">O+</option><option value="O-">O-</option></select></div>
                        <div class="col-md-6"><label class="form-label">Seguro Médico (EPS)</label><input type="text" name="seguro_medico" class="form-control" value="<?= htmlspecialchars($datos_paciente['seguro_medico'] ?? '') ?>"></div>
                        <div class="col-md-6"><label class="form-label">Número de Afiliación</label><input type="text" name="numero_seguro" class="form-control" value="<?= htmlspecialchars($datos_paciente['numero_seguro'] ?? '') ?>"></div>
                    </div>
                </fieldset>

                <fieldset>
                    <legend><i class="fas fa-users-cog me-2"></i>Asignaciones</legend>
                    <div class="row g-3">
                        <div class="col-md-6"><label for="id_usuario_cuidador" class="form-label">Asignar Cuidador</label><select name="id_usuario_cuidador" id="id_usuario_cuidador" class="form-select" required><option value="">Seleccione un Cuidador</option><?php foreach ($lista_cuidadores as $cuidador): ?><option value="<?= $cuidador['id_usuario'] ?>" <?= ($asignacion_activa && $asignacion_activa['id_usuario_cuidador'] == $cuidador['id_usuario']) ? 'selected' : '' ?>><?= htmlspecialchars($cuidador['nombre'] . ' ' . $cuidador['apellido']) ?></option><?php endforeach; ?></select></div>
                        <div class="col-md-6"><label for="id_usuario_familiar" class="form-label">Enlazar Familiar (Opcional)</label><select name="id_usuario_familiar" id="id_usuario_familiar" class="form-select"><option value="">Ninguno</option><?php foreach ($lista_familiares as $familiar):?><option value="<?=$familiar['id_usuario']?>" <?= (isset($datos_paciente['id_usuario_familiar']) && $datos_paciente['id_usuario_familiar'] == $familiar['id_usuario']) ? 'selected' : '' ?>><?= htmlspecialchars($familiar['nombre'].' '.$familiar['apellido']) ?></option><?php endforeach;?></select></div>
                        <div class="col-12"><label for="descripcion_asignacion" class="form-label">Descripción de la Asignación</label><textarea name="descripcion_asignacion" id="descripcion_asignacion" rows="3" class="form-control chat-bubble-input" placeholder="Ej: Asignado para cubrir turno. Paciente requiere supervisión constante."><?= htmlspecialchars($asignacion_activa['descripcion'] ?? '') ?></textarea></div>
                    </div>
                </fieldset>
                
                <div class="form-actions">
                    <a href="admin_pacientes.php" class="btn-action-cancel">Cancelar</a>
                    <button type="submit" class="btn-action-submit"><i class="fas fa-save me-2"></i><?= $modo_edicion ? 'Actualizar Cambios' : 'Guardar Paciente' ?></button>
                </div>
            </form>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    <script src="../js_admin/admin_scripts.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const tipoSangre = "<?= $datos_paciente['tipo_sangre'] ?? '' ?>";
        if (tipoSangre) {
            const option = document.querySelector(`#tipo_sangre option[value="${tipoSangre}"]`);
            if (option) option.selected = true;
        }

        <?php if(isset($_SESSION['error'])): ?>
            Swal.fire({ icon: 'error', title: 'Error al Guardar', text: '<?= addslashes($_SESSION['error']) ?>' });
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
    });
    </script>
</body>
</html>
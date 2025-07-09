<?php
session_start();
// Tu seguridad y lógica PHP
require_once __DIR__ . '/../../../models/clases/pacientes.php';
require_once __DIR__ . '/../../../models/clases/usuario.php';

// --- ESTA SECCIÓN ES NUEVA ---
// Se necesita para obtener la lista de cuidadores y familiares
$user_model = new usuario();
$lista_familiares = $user_model->obtenerUsuariosPorRol('Familiar');
$lista_cuidadores = $user_model->obtenerUsuariosPorRol('Cuidador');
// --- FIN DE LA SECCIÓN NUEVA ---

// El resto de tu lógica para la página
$modo_edicion = false; // En agregar_paciente, nunca estamos en modo edición
$datos_paciente = [];
$asignacion_activa = null; 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Paciente - GeriCare</title>
    
    <link rel="stylesheet" href="../../libs/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link rel="stylesheet" href="../css_admin/admin_main.css?v=<?= time(); ?>">
    <link rel="stylesheet" href="../css_admin/agregar_paciente.css?v=<?= time(); ?>">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

    <header class="header">
        </header>

    <main class="container mt-5">
        <div class="form-container-card animate__animated animate__fadeInUp">
            <h1 class="form-title-main"><i class="fas fa-user-plus"></i> Registrar Nuevo Paciente</h1>
            
            <form action="../../../controllers/admin/paciente_controller.php" method="POST">
                <input type="hidden" name="accion" value="registrar">
                
                <fieldset class="mb-4">
                    <legend><i class="fas fa-user-circle me-2"></i>Datos Personales</legend>
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label">Documento</label><input type="number" name="documento_identificacion" class="form-control" required></div>
                        <div class="col-md-6"><label class="form-label">Nombres</label><input type="text" name="nombre" class="form-control" required></div>
                        <div class="col-md-6"><label class="form-label">Apellidos</label><input type="text" name="apellido" class="form-control" required></div>
                        <div class="col-md-6"><label class="form-label">Fecha de Nacimiento</label><input type="date" name="fecha_nacimiento" class="form-control" required></div>
                        <div class="col-md-6"><label class="form-label">Género</label><select name="genero" class="form-select" required><option value="">Seleccione...</option><option value="Masculino">Masculino</option><option value="Femenino">Femenino</option></select></div>
                        <div class="col-md-6"><label class="form-label">Contacto de Emergencia</label><input type="text" name="contacto_emergencia" class="form-control" required></div>
                    </div>
                </fieldset>

                <fieldset class="mb-4">
                    <legend><i class="fas fa-heartbeat me-2"></i>Información Adicional</legend>
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label">Tipo de Sangre</label><select name="tipo_sangre" id="tipo_sangre" class="form-select" required><option value="">Seleccione...</option><option value="A+">A+</option><option value="A-">A-</option><option value="B+">B+</option><option value="B-">B-</option><option value="AB+">AB+</option><option value="AB-">AB-</option><option value="O+">O+</option><option value="O-">O-</option></select></div>
                        <div class="col-md-6"><label class="form-label">Estado Civil</label><input type="text" name="estado_civil" class="form-control" required></div>
                        <div class="col-md-6"><label class="form-label">Seguro Médico (EPS)</label><input type="text" name="seguro_medico" class="form-control"></div>
                        <div class="col-md-6"><label class="form-label">Número de Afiliación</label><input type="text" name="numero_seguro" class="form-control"></div>
                    </div>
                </fieldset>

                <fieldset>
                    <legend><i class="fas fa-users-cog me-2"></i>Asignaciones</legend>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="id_usuario_cuidador" class="form-label">Asignar Cuidador</label>
                            <select name="id_usuario_cuidador" id="id_usuario_cuidador" class="form-select" required>
                                <option value="">Seleccione un Cuidador</option>
                                <?php foreach ($lista_cuidadores as $cuidador): ?>
                                    <option value="<?= $cuidador['id_usuario'] ?>">
                                        <?= htmlspecialchars($cuidador['nombre'] . ' ' . $cuidador['apellido']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="id_usuario_familiar" class="form-label">Enlazar Familiar (Opcional)</label>
                            <select name="id_usuario_familiar" id="id_usuario_familiar" class="form-select">
                                <option value="">Ninguno</option>
                                <?php foreach ($lista_familiares as $familiar):?>
                                    <option value="<?=$familiar['id_usuario']?>">
                                        <?= htmlspecialchars($familiar['nombre'].' '.$familiar['apellido']) ?>
                                    </option>
                                <?php endforeach;?>
                            </select>
                        </div>
                        <div class="col-12">
                            <label for="descripcion_asignacion" class="form-label">Descripción de la Asignación</label>
                            <textarea name="descripcion_asignacion" id="descripcion_asignacion" rows="3" class="form-control chat-bubble-input" placeholder="Ej: Asignado para cubrir turno. Paciente requiere supervisión constante."></textarea>
                        </div>
                    </div>
                </fieldset>
                
                <div class="form-actions">
                    <a href="admin_pacientes.php" class="btn-action-cancel">Cancelar</a>
                    <button type="submit" class="btn-action-submit"><i class="fas fa-save me-2"></i>Guardar Paciente</button>
                </div>
            </form>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    <script src="../js_admin/admin_scripts.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Muestra alerta de error si existe en la sesión
        <?php if(isset($_SESSION['error'])): ?>
            Swal.fire({ 
                icon: 'error', 
                title: 'Error al Guardar', 
                text: '<?= addslashes($_SESSION['error']) ?>',
                confirmButtonColor: '#007bff'
            });
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        // Muestra alerta de éxito si existe en la sesión
        <?php if(isset($_SESSION['success'])): ?>
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: '<?= addslashes($_SESSION['success']) ?>',
                timer: 2000,
                showConfirmButton: false
            });
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
    });
    </script>
</body>
</html>
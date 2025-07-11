<?php
require_once __DIR__ . '/../../../controllers/auth/verificar_sesion.php';
require_once __DIR__ . '/../../../models/clases/historia_clinica.php';
require_once __DIR__ . '/../../../models/clases/pacientes.php';
require_once __DIR__ . '/../../../models/clases/medicamento.modelo.php';
require_once __DIR__ . '/../../../models/clases/enfermedad.modelo.php';

verificarAcceso(['Administrador']);

$modelo_hc = new HistoriaClinica();
$modelo_paciente = new Paciente();
$modelo_medicamento = new ModeloMedicamentos();
$modelo_enfermedad = new ModeloEnfermedades();

$modo_edicion = false;
$datos_hc = [];
$enfermedades_asignadas = [];
$medicamentos_asignados = [];

$pacientes = $modelo_paciente->consultar();
$lista_completa_medicamentos = $modelo_medicamento->mdlMostrarMedicamentos('tb_medicamento', null, null);
$lista_completa_enfermedades = $modelo_enfermedad->mdlMostrarEnfermedades('tb_enfermedad', null, null);

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $modo_edicion = true;
    $id_historia_clinica = $_GET['id'];
    $datos_hc = $modelo_hc->obtenerHistoriaPorId($id_historia_clinica);

    if (!$datos_hc) {
        $_SESSION['error'] = "No se encontró la historia clínica solicitada.";
        header("Location: historia_clinica.php");
        exit();
    }
    $enfermedades_asignadas = $modelo_hc->consultarEnfermedadesAsignadas($id_historia_clinica);
    $medicamentos_asignados = $modelo_hc->consultarMedicamentosAsignados($id_historia_clinica);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= $modo_edicion ? 'Gestionar' : 'Crear' ?> Historia Clínica</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="stylesheet" href="../css_admin/gestion_hc_detallada.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
    .form-group textarea, textarea#descripcion .form-asignar-medicamento{
        resize: none !important; 
        min-height: 70px;
    }
.form-group textarea .medicamentos .form-asignar-medicamento{
    
    /* Evita que el usuario pueda cambiar el tamaño del textarea */
    resize: none; 
    
    /* Define una altura inicial para que no se vea tan pequeño */
    min-height: 50px; 
    
    width: 100%;
    padding: 10px;
    border: 1px solid #ced4da;
    border-radius: 5px;
    font-family: 'Poppins', sans-serif; /* Asegura que use la misma fuente */
    font-size: 1rem;
    transition: border-color 0.2s ease, box-shadow 0.2s ease; /* Transición suave */
}

.form-group textarea:focus .me{
    border-color: #80bdff;
    outline: 0;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}
</style>
</head>
<body>
    <div class="main-container">
        <h1>
            <i class="fas fa-file-signature"></i>
            <?= $modo_edicion ? 'Gestionar Historia de: ' . htmlspecialchars($datos_hc['paciente_nombre_completo']) : 'Nueva Historia Clínica' ?>
        </h1>

        <form id="form-hc-principal" action="../../../controllers/admin/HC/historia_clinica_controller.php" method="POST">
            <input type="hidden" name="accion" value="<?= $modo_edicion ? 'actualizar' : 'registrar' ?>">
            <?php if ($modo_edicion): ?>
                <input type="hidden" name="id_historia_clinica" value="<?= htmlspecialchars($datos_hc['id_historia_clinica']) ?>">
            <?php endif; ?>

            <div class="form-section">
                <h3><i class="fas fa-notes-medical"></i> Datos Generales</h3>
                <div class="form-grid">
                    <div class="form-group full-width">
                        <label for="id_paciente">Paciente</label>
                        <select name="id_paciente" id="id_paciente" required <?= $modo_edicion ? 'disabled' : '' ?>>
                            <option value="">-- Seleccione un paciente --</option>
                            <?php foreach ($pacientes as $paciente): ?>
                                <option value="<?= $paciente['id_paciente'] ?>" <?= (isset($datos_hc['id_paciente']) && $datos_hc['id_paciente'] == $paciente['id_paciente']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($paciente['nombre'] . ' ' . $paciente['apellido']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                         <?php if ($modo_edicion): ?>
                            <input type="hidden" name="id_paciente" value="<?= htmlspecialchars($datos_hc['id_paciente']) ?>">
                        <?php endif; ?>
                    </div>
                    
                    <!-- CAMPOS RESTAURADOS -->
                    <div class="form-group full-width">
                        <label for="estado_salud">Estado de Salud General</label>
                        <textarea name="estado_salud" id="estado_salud" rows="3" required><?= htmlspecialchars($datos_hc['estado_salud'] ?? '') ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="condiciones">Condiciones Médicas</label>
                        <textarea name="condiciones" id="condiciones" rows="3"><?= htmlspecialchars($datos_hc['condiciones'] ?? '') ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="antecedentes_medicos">Antecedentes Médicos</label>
                        <textarea name="antecedentes_medicos" id="antecedentes_medicos" rows="3"><?= htmlspecialchars($datos_hc['antecedentes_medicos'] ?? '') ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="alergias">Alergias</label>
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
                    <a href="historia_clinica.php" class="btn btn-secondary">Volver a la Lista</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> <?= $modo_edicion ? 'Actualizar Datos' : 'Guardar y Continuar' ?>
                    </button>
                </div>
            </div>
        </form>

        <?php if ($modo_edicion): ?>
            <div id="secciones-asignacion" data-id-hc="<?= $datos_hc['id_historia_clinica'] ?>">
                <div class="form-section">
                    <h3><i class="fas fa-disease"></i> Enfermedades Diagnosticadas</h3>
                    <div class="lista-asignados" id="lista-enfermedades-asignadas">
                        <?php if (empty($enfermedades_asignadas)): ?>
                            <p class="empty-message">No hay enfermedades asignadas.</p>
                        <?php else: ?>
                            <?php foreach ($enfermedades_asignadas as $enf): ?>
                                <div class="item-asignado" id="enf-<?= $enf['id_hc_enfermedad'] ?>">
                                    <span><?= htmlspecialchars($enf['nombre_enfermedad']) ?></span>
                                    <button class="btn-delete" onclick="eliminarAsignacion(<?= $enf['id_hc_enfermedad'] ?>, 'enfermedad')"><i class="fas fa-trash"></i></button>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <div class="form-asignar">
                        <select id="select-enfermedad">
                            <option value="">Buscar enfermedad...</option>
                            <?php foreach ($lista_completa_enfermedades as $enf): ?>
                                <option value="<?= $enf['id_enfermedad'] ?>"><?= htmlspecialchars($enf['nombre_enfermedad']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button class="btn btn-success" onclick="asignarEnfermedad()">Asignar</button>
                        <a href="gestion_enfermedades.php" target="_blank" class="btn btn-link">Crear Nueva</a>
                    </div>
                </div>
                <div class="form-section">
                    <h3><i class="fas fa-pills"></i> Medicamentos Recetados</h3>
                    <div class="lista-asignados" id="lista-medicamentos-asignados">
                         <?php if (empty($medicamentos_asignados)): ?>
                            <p class="empty-message">No hay medicamentos recetados.</p>
                        <?php else: ?>
                            <?php foreach ($medicamentos_asignados as $med): ?>
                                <div class="item-asignado-medicamento" id="med-<?= $med['id_hc_medicamento'] ?>">
                                    <div class="medicamento-info">
                                        <strong><?= htmlspecialchars($med['nombre_medicamento']) ?></strong>
                                        <small>Dosis: <?= htmlspecialchars($med['dosis']) ?> | Frecuencia: <?= htmlspecialchars($med['frecuencia']) ?></small>
                                        <p>Instrucciones: <?= htmlspecialchars($med['instrucciones']) ?></p>
                                    </div>
                                    <div class="medicamento-actions">
                                        <button class="btn-edit" onclick='editarMedicamento(<?= json_encode($med) ?>)'><i class="fas fa-edit"></i></button>
                                        <button class="btn-delete" onclick="eliminarAsignacion(<?= $med['id_hc_medicamento'] ?>, 'medicamento')"><i class="fas fa-trash"></i></button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <form id="form-asignar-medicamento" class="form-asignar-medicamento">
                        <select id="select-medicamento" required>
                             <option value="">Buscar medicamento...</option>
                             <?php foreach ($lista_completa_medicamentos as $med): ?>
                                <option value="<?= $med['id_medicamento'] ?>"><?= htmlspecialchars($med['nombre_medicamento']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <input type="text" id="input-dosis" placeholder="Dosis (ej. 500mg)" required>
                        <input type="text" id="input-frecuencia" placeholder="Frecuencia (ej. cada 8 horas)" required>
                        <textarea class="medicamentos" id="input-instrucciones" placeholder="Instrucciones adicionales..." rows="2"></textarea>
                        <div class="form-actions-inline">
                           <button type="submit" class="btn btn-success">Recetar Medicamento</button>
                           <a href="gestion_medicamentos.php" target="_blank" class="btn btn-link">Crear Nuevo</a>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <script src="../js_admin/gestion_hc_detallada.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            <?php if(isset($_SESSION['mensaje'])): ?>
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: '<?= addslashes($_SESSION['mensaje']) ?>',
                    timer: 2500,
                    showConfirmButton: false
                });
                <?php unset($_SESSION['mensaje']); ?>
            <?php endif; ?>

            <?php if(isset($_SESSION['error'])): ?>
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: '<?= addslashes($_SESSION['error']) ?>'
                });
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
        });
    </script>
</body>
</html>
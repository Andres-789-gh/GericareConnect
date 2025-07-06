<?php
require_once __DIR__ . '/../../../../controllers/admin/HC/historia_clinica_controlador.php';
require_once __DIR__ . '/../../../../models/clases/pacientes.php'; // Para obtener la lista de pacientes

// Inicializar variables
$historia = null;
$editMode = false;
$pageTitle = "Registrar Historia Clínica";
$action = "registrar";

$controller = new HistoriaClinicaController();
$pacienteModel = new Paciente(); // Asumiendo que tienes un método para listar pacientes
$pacientes = $pacienteModel->consultarPacientesActivos(); // Necesitas un método que devuelva los pacientes

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_historia = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
    $historia = $controller->obtenerHistoriaClinicaPorId($id_historia);
    if ($historia) {
        $editMode = true;
        $pageTitle = "Editar Historia Clínica #" . $historia['id_historia_clinica'];
        $action = "editar";
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GeriCare Connect | <?php echo $pageTitle; ?></title>
    <link rel="stylesheet" href="../css_admin/historia_clinica_form.css"> <!-- Se recomienda un CSS específico para el formulario -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <div class="container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="logo">
                <img src="../../../../assets/img/logo.png" alt="Logo">
                <h2>GeriCare Connect</h2>
            </div>
            <nav class="menu">
                 <a href="#"><i class="fas fa-home"></i> Dashboard</a>
                <a href="admin_pacientes.php"><i class="fas fa-user-injured"></i> Pacientes</a>
                <a href="historia_clinica.php" class="active"><i class="fas fa-file-medical"></i> Historia Clínica</a>
                <a href="agendar_citas.php"><i class="fas fa-calendar-alt"></i> Agendar Citas</a>
                <a href="registrar_empleado.php"><i class="fas fa-user-plus"></i> Registrar Empleados</a>
                <a href="admin_solicitudes.php"><i class="fas fa-tasks"></i> Solicitudes</a>
                <a href="../../../../controllers/admin/logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="main-header">
                <h1><?php echo $pageTitle; ?></h1>
            </header>

            <div class="content-form">
                <form id="form-historia-clinica" action="../../../../controllers/admin/HC/historia_clinica_controlador.php" method="POST">
                    <!-- Campo oculto para la acción (registrar o editar) -->
                    <input type="hidden" name="action" value="<?php echo $action; ?>">
                    
                    <?php if ($editMode) : ?>
                        <!-- Campo oculto para el ID en modo edición -->
                        <input type="hidden" name="id_historia_clinica" value="<?php echo $historia['id_historia_clinica']; ?>">
                    <?php endif; ?>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="id_paciente">Paciente</label>
                            <select id="id_paciente" name="id_paciente" required <?php echo $editMode ? 'disabled' : ''; ?>>
                                <option value="">Seleccione un paciente</option>
                                <?php foreach ($pacientes as $paciente) : ?>
                                    <option value="<?php echo $paciente['id_paciente']; ?>" <?php echo ($editMode && $historia['id_paciente'] == $paciente['id_paciente']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($paciente['nombre'] . ' ' . $paciente['apellido'] . ' (C.C: ' . $paciente['documento'] . ')'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                             <?php if ($editMode) : ?>
                                <small>El paciente no se puede cambiar una vez creada la historia clínica.</small>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="fecha_creacion">Fecha de Creación</label>
                            <input type="date" id="fecha_creacion" name="fecha_creacion" value="<?php echo $editMode ? $historia['fecha_creacion'] : date('Y-m-d'); ?>" required>
                        </div>

                        <div class="form-group full-width">
                            <label for="motivo_consulta">Motivo de la Consulta</label>
                            <textarea id="motivo_consulta" name="motivo_consulta" rows="4" required><?php echo $editMode ? htmlspecialchars($historia['motivo_consulta']) : ''; ?></textarea>
                        </div>

                        <div class="form-group full-width">
                            <label for="antecedentes_medicos">Antecedentes Médicos</label>
                            <textarea id="antecedentes_medicos" name="antecedentes_medicos" rows="4"><?php echo $editMode ? htmlspecialchars($historia['antecedentes_medicos']) : ''; ?></textarea>
                        </div>
                        
                        <div class="form-group full-width">
                            <label for="examen_fisico">Examen Físico</label>
                            <textarea id="examen_fisico" name="examen_fisico" rows="4"><?php echo $editMode ? htmlspecialchars($historia['examen_fisico']) : ''; ?></textarea>
                        </div>

                        <div class="form-group full-width">
                            <label for="diagnostico">Diagnóstico</label>
                            <textarea id="diagnostico" name="diagnostico" rows="4"><?php echo $editMode ? htmlspecialchars($historia['diagnostico']) : ''; ?></textarea>
                        </div>

                        <div class="form-group full-width">
                            <label for="plan_tratamiento">Plan de Tratamiento</label>
                            <textarea id="plan_tratamiento" name="plan_tratamiento" rows="4"><?php echo $editMode ? htmlspecialchars($historia['plan_tratamiento']) : ''; ?></textarea>
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="historia_clinica.php" class="btn btn-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> <?php echo $editMode ? 'Actualizar Historia' : 'Guardar Historia'; ?>
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>

</html>

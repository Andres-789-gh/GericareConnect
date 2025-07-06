<?php
require_once __DIR__ . '/../../../controllers/auth/verificar_sesion.php';
require_once __DIR__ . '/../../../models/clases/historia_clinica.php';

verificarAcceso(['Administrador']);

// Validar que se reciba un ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "ID de historia clínica no válido.";
    header("Location: historia_clinica.php");
    exit();
}

$id_historia_clinica = $_GET['id'];
$modelo_hc = new HistoriaClinica();

// Obtener todos los datos para el reporte
$datos_hc = $modelo_hc->obtenerReporteCompleto($id_historia_clinica);
$enfermedades = $modelo_hc->consultarEnfermedadesAsignadas($id_historia_clinica);
$medicamentos = $modelo_hc->consultarMedicamentosAsignados($id_historia_clinica);

if (!$datos_hc) {
    $_SESSION['error'] = "No se encontró la historia clínica para generar el reporte.";
    header("Location: historia_clinica.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Historia Clínica</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="stylesheet" href="../css_admin/reporte_hc.css">
</head>
<body>
    <div class="report-container">
        <div class="report-header">
            <h1><i class="fas fa-file-medical-alt"></i> Reporte de Historia Clínica</h1>
            <div class="header-info">
                <span>Paciente: <strong><?= htmlspecialchars($datos_hc['paciente_nombre'] . ' ' . $datos_hc['paciente_apellido']) ?></strong></span>
                <span>ID de HC: <strong><?= htmlspecialchars($datos_hc['id_historia_clinica']) ?></strong></span>
            </div>
        </div>

        <div class="report-section">
            <h2><i class="fas fa-user-injured"></i> Datos del Paciente</h2>
            <div class="details-grid">
                <p><strong>Nombre Completo:</strong> <?= htmlspecialchars($datos_hc['paciente_nombre'] . ' ' . $datos_hc['paciente_apellido']) ?></p>
                <p><strong>Documento:</strong> <?= htmlspecialchars($datos_hc['paciente_documento']) ?></p>
                <p><strong>Fecha de Nacimiento:</strong> <?= htmlspecialchars(date("d/m/Y", strtotime($datos_hc['paciente_fecha_nacimiento']))) ?></p>
            </div>
        </div>

        <div class="report-section">
            <h2><i class="fas fa-notes-medical"></i> Detalles de la Historia Clínica</h2>
            <div class="details-grid">
                 <p><strong>Gestionado por:</strong> <?= htmlspecialchars($datos_hc['admin_nombre'] . ' ' . $datos_hc['admin_apellido']) ?></p>
                 <p><strong>Última Consulta:</strong> <?= htmlspecialchars(date("d/m/Y", strtotime($datos_hc['fecha_ultima_consulta']))) ?></p>
            </div>
            <div class="detail-block">
                <h4>Estado de Salud General</h4>
                <p><?= nl2br(htmlspecialchars($datos_hc['estado_salud'])) ?></p>
            </div>
            <div class="detail-block">
                <h4>Condiciones Médicas</h4>
                <p><?= nl2br(htmlspecialchars($datos_hc['condiciones'] ?: 'No especificado')) ?></p>
            </div>
            <div class="detail-block">
                <h4>Antecedentes Médicos</h4>
                <p><?= nl2br(htmlspecialchars($datos_hc['antecedentes_medicos'] ?: 'No especificado')) ?></p>
            </div>
            <div class="detail-block">
                <h4>Alergias</h4>
                <p><?= nl2br(htmlspecialchars($datos_hc['alergias'] ?: 'No especificado')) ?></p>
            </div>
             <div class="detail-block">
                <h4>Dietas Especiales</h4>
                <p><?= nl2br(htmlspecialchars($datos_hc['dietas_especiales'] ?: 'No especificado')) ?></p>
            </div>
            <div class="detail-block">
                <h4>Observaciones</h4>
                <p><?= nl2br(htmlspecialchars($datos_hc['observaciones'] ?: 'No especificado')) ?></p>
            </div>
        </div>

        <div class="report-section">
            <h2><i class="fas fa-disease"></i> Enfermedades Diagnosticadas</h2>
            <?php if (empty($enfermedades)): ?>
                <p>No hay enfermedades registradas en esta historia clínica.</p>
            <?php else: ?>
                <ul>
                    <?php foreach ($enfermedades as $enf): ?>
                        <li><?= htmlspecialchars($enf['nombre_enfermedad']) ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

        <div class="report-section">
            <h2><i class="fas fa-pills"></i> Medicamentos Recetados</h2>
            <?php if (empty($medicamentos)): ?>
                <p>No hay medicamentos recetados en esta historia clínica.</p>
            <?php else: ?>
                <div class="medicamentos-list">
                    <?php foreach ($medicamentos as $med): ?>
                        <div class="medicamento-item">
                            <h4><?= htmlspecialchars($med['nombre_medicamento']) ?></h4>
                            <p><strong>Dosis:</strong> <?= htmlspecialchars($med['dosis']) ?></p>
                            <p><strong>Frecuencia:</strong> <?= htmlspecialchars($med['frecuencia']) ?></p>
                            <p><strong>Instrucciones:</strong> <?= nl2br(htmlspecialchars($med['instrucciones'] ?: 'Sin instrucciones adicionales.')) ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="report-footer">
            <button onclick="window.print()" class="btn btn-primary"><i class="fas fa-print"></i> Imprimir Reporte</button>
            <a href="historia_clinica.php" class="btn btn-secondary">Volver a la Lista</a>
        </div>
    </div>
</body>
</html>
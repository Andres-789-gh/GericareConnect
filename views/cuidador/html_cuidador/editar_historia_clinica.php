<?php
// views/cuidador/html_cuidador/editar_historia_clinica.php
session_start();

require_once __DIR__ . "/../../../controllers/cuidador/historia_clinica.controlador.php";
require_once __DIR__ . "/../../../models/clases/historia_clinica.modelo.php";

$idHistoriaClinica = isset($_GET['idHistoriaClinica']) ? (int)$_GET['idHistoriaClinica'] : 0;
$historiaClinica = null;

if ($idHistoriaClinica > 0) {
    $historiaClinica = ControladorHistoriaClinica::ctrMostrarHistoriasClinicas('id_historia_clinica', $idHistoriaClinica);
}

if (!$historiaClinica) {
    echo "<h1>Error: Historia clínica no encontrada.</h1>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Historia Clínica</title>
    <link rel="stylesheet" href="../../css/styles.css">
    <link rel="stylesheet" href="../css_cuidador/historia_clinica.css">
</head>
<body>
    <div class="container">
        <h1>Editar Historia de "<?php echo htmlspecialchars($historiaClinica['paciente_nombre_completo']); ?>"</h1>

        <div class="form-container">
            <form method="post" action="historia_clinica.php">
                <input type="hidden" name="id_historia_clinica_editar" value="<?php echo htmlspecialchars($idHistoriaClinica); ?>">
                
                <div class="form-grid">
                    <div class="form-column">
                        <div class="form-group">
                            <label for="estado_salud">Estado de Salud General:</label>
                            <textarea name="estado_salud" rows="3"><?php echo htmlspecialchars($historiaClinica['estado_salud'] ?? ''); ?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="antecedentes_medicos">Antecedentes Médicos:</label>
                            <textarea name="antecedentes_medicos" rows="2"><?php echo htmlspecialchars($historiaClinica['antecedentes_medicos'] ?? ''); ?></textarea>
                        </div>
                         <div class="form-group">
                            <label for="alergias">Alergias Conocidas:</label>
                            <textarea name="alergias" rows="2"><?php echo htmlspecialchars($historiaClinica['alergias'] ?? ''); ?></textarea>
                        </div>
                    </div>
                    <div class="form-column">
                        <div class="form-group">
                            <label for="condiciones">Condiciones Crónicas:</label>
                            <textarea name="condiciones" rows="3"><?php echo htmlspecialchars($historiaClinica['condiciones'] ?? ''); ?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="dietas_especiales">Dietas Especiales:</label>
                            <textarea name="dietas_especiales" rows="2"><?php echo htmlspecialchars($historiaClinica['dietas_especiales'] ?? ''); ?></textarea>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="observaciones">Observaciones Adicionales:</label>
                    <textarea name="observaciones" rows="4"><?php echo htmlspecialchars($historiaClinica['observaciones'] ?? ''); ?></textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Actualizar Historia Clínica</button>
                    <a href="historia_clinica.php" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
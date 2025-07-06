<?php
// --- Inicia la sesión y verifica los permisos de administrador ---
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../../controllers/auth/verificar_sesion.php';
verificarAcceso(['Administrador']);

// --- Incluye el controlador ---
require_once __DIR__ . "/../../../controllers/admin/HC/historia_clinica_controlador.php";

// --- Instancia el controlador ---
$controlador = new ControladorHistoriaClinica();

// --- Procesa la actualización si el formulario se envía con POST ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] == 'actualizar') {
    $controlador->actualizar();
}

// --- Obtiene el ID de la URL y busca los datos para rellenar el formulario ---
$idHistoriaClinica = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($idHistoriaClinica === 0) {
    $_SESSION['error'] = "ID de historia clínica no especificado.";
    header("Location: historia_clinica.php");
    exit();
}

// Obtiene los datos de la historia clínica a editar
$historiaArray = $controlador->mostrar('id_historia_clinica', $idHistoriaClinica);
$historiaClinica = $historiaArray[0] ?? null; // El SP puede devolver un array

if (!$historiaClinica) {
    $_SESSION['error'] = "No se encontró la historia clínica con ID {$idHistoriaClinica}.";
    header("Location: historia_clinica.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Historia Clínica #<?= htmlspecialchars($idHistoriaClinica) ?></title>
    <link rel="stylesheet" href="../../css/styles.css">
    <link rel="stylesheet" href="../../cuidador/css_cuidador/historia_clinica.css"> </head>
<body>
    <div class="container">
        <h1>Editar Historia Clínica de "<?= htmlspecialchars($historiaClinica['paciente_nombre_completo']); ?>"</h1>
        
        <div class="form-container">
            <form method="post" action="editar_historia_clinica.php?id=<?= $idHistoriaClinica ?>">
                <input type="hidden" name="accion" value="actualizar">
                <input type="hidden" name="id_historia_clinica" value="<?= $idHistoriaClinica ?>">

                <div class="form-grid">
                    <div class="form-column">
                        <div class="form-group"><label>Estado de Salud:</label><textarea name="estado_salud" rows="3"><?= htmlspecialchars($historiaClinica['estado_salud'] ?? '') ?></textarea></div>
                        <div class="form-group"><label>Antecedentes Médicos:</label><textarea name="antecedentes_medicos" rows="2"><?= htmlspecialchars($historiaClinica['antecedentes_medicos'] ?? '') ?></textarea></div>
                    </div>
                    <div class="form-column">
                        <div class="form-group"><label>Condiciones Crónicas:</label><textarea name="condiciones" rows="3"><?= htmlspecialchars($historiaClinica['condiciones'] ?? '') ?></textarea></div>
                        <div class="form-group"><label>Alergias Conocidas:</label><textarea name="alergias" rows="2"><?= htmlspecialchars($historiaClinica['alergias'] ?? '') ?></textarea></div>
                    </div>
                </div>

                <div class="form-group"><label>Dietas Especiales:</label><textarea name="dietas_especiales" rows="2"><?= htmlspecialchars($historiaClinica['dietas_especiales'] ?? '') ?></textarea></div>
                <div class="form-group"><label>Observaciones Adicionales:</label><textarea name="observaciones" rows="4"><?= htmlspecialchars($historiaClinica['observaciones'] ?? '') ?></textarea></div>
                
                <div class="form-actions">
                    <a href="historia_clinica.php" class="btn btn-secondary">Volver al Listado</a>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
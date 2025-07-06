<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../../controllers/auth/verificar_sesion.php';
verificarAcceso(['Administrador']);
require_once __DIR__ . "/../../../controllers/admin/HC/historia_clinica.controlador.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["id_paciente"])) {
    ControladorHistoriaClinica::ctrCrearHistoriaClinica();
}
if (isset($_GET['idHistoriaClinicaEliminar'])) {
    $_GET['idHistoriaClinica'] = $_GET['idHistoriaClinicaEliminar'];
    ControladorHistoriaClinica::ctrEliminarHistoriaClinica();
}
$modelo = new ModeloHistoriaClinica();
$pacientes = $modelo->mdlObtenerPacientesActivos();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"><title>Gestión de Historias Clínicas</title>
    <link rel="stylesheet" href="../../css/styles.css"><link rel="stylesheet" href="../css_admin/admin_pacientes.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
</head>
<body>
    <div class="container">
        <h1>Gestión de Historias Clínicas</h1>
        <div class="form-container">
            <form method="post" action="historia_clinica.php">
                <h2>Registrar Nueva Historia Clínica</h2>
                <div class="form-group">
                    <label for="id_paciente">Paciente:</label>
                    <select name="id_paciente" id="id_paciente" class="select2-paciente" required style="width: 100%;">
                        <option value="">Seleccione un paciente...</option>
                        <?php foreach ($pacientes as $paciente): ?>
                            <option value="<?= $paciente['id_paciente'] ?>"><?= htmlspecialchars($paciente['nombre_completo']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-grid">
                    <div class="form-column">
                        <div class="form-group"><label>Estado de Salud General:</label><textarea name="estado_salud" rows="3"></textarea></div>
                        <div class="form-group"><label>Antecedentes Médicos:</label><textarea name="antecedentes_medicos" rows="2"></textarea></div>
                    </div>
                    <div class="form-column">
                        <div class="form-group"><label>Condiciones Crónicas:</label><textarea name="condiciones" rows="3"></textarea></div>
                        <div class="form-group"><label>Alergias Conocidas:</label><textarea name="alergias" rows="2"></textarea></div>
                    </div>
                </div>
                <div class.form-group"><label>Dietas Especiales:</label><textarea name="dietas_especiales" rows="2"></textarea></div>
                <div class="form-group"><label>Observaciones Adicionales:</label><textarea name="observaciones" rows="4"></textarea></div>
                <div class="form-actions"><button type="submit" class="btn btn-primary">Crear y Continuar</button></div>
            </form>
        </div>
        <div class="table-container">
            <h2>Historias Clínicas Registradas</h2>
            <table>
                <thead><tr><th>ID</th><th>Paciente</th><th>Fecha</th><th>Medicamentos</th><th>Enfermedades</th><th>Acciones</th></tr></thead>
                <tbody>
                    <?php foreach (ControladorHistoriaClinica::ctrMostrarHistoriasClinicas(null, null) as $historia): ?>
                        <tr>
                            <td><?= htmlspecialchars($historia["id_historia_clinica"]) ?></td>
                            <td><?= htmlspecialchars($historia["paciente_nombre_completo"]) ?></td>
                            <td><?= htmlspecialchars($historia["fecha_formateada"]) ?></td>
                            <td><?= htmlspecialchars($historia["medicamentos"] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($historia["enfermedades"] ?? 'N/A') ?></td>
                            <td>
                                <a href="editar_historia_clinica.php?idHistoriaClinica=<?= $historia['id_historia_clinica'] ?>" class="btn btn-warning">Editar/Ver</a>
                                <a href="historia_clinica.php?idHistoriaClinicaEliminar=<?= $historia['id_historia_clinica'] ?>" class="btn btn-danger" onclick="return confirm('¿Confirmas la eliminación?');">Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script>$(document).ready(function() { $('.select2-paciente').select2({ placeholder: "Escriba o seleccione un paciente", allowClear: true }); });</script>
</body>
</html>
<?php
// --- Inicia la sesión y verifica los permisos de administrador ---
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../../controllers/auth/verificar_sesion.php';
verificarAcceso(['Administrador']); // Solo administradores pueden acceder

// --- Incluye el controlador y el modelo necesarios ---
require_once __DIR__ . "/../../../controllers/admin/HC/historia_clinica_controlador.php";
require_once __DIR__ . "/../../../models/clases/pacientes.php"; // Para obtener la lista de pacientes

// --- Instancia el controlador para usarlo en la vista ---
$controlador = new ControladorHistoriaClinica();

// --- Lógica para procesar las acciones del formulario ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["accion"])) {
    if ($_POST['accion'] == 'registrar') {
        $controlador->registrar();
    }
}
// Procesa la eliminación si se pasa el ID por GET
if (isset($_GET['idHistoriaEliminar'])) {
    $controlador->eliminar();
}

// --- Obtiene los datos necesarios para la vista ---
$historias_clinicas = $controlador->mostrar(); // Llama al método del controlador para obtener todas las historias
$paciente_model = new Paciente();
$pacientes_activos = $paciente_model->consultar(); // Obtiene pacientes para el dropdown

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Historias Clínicas</title>
    <link rel="stylesheet" href="../../css/styles.css">
    <link rel="stylesheet" href="../css_admin/admin_pacientes.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
</head>
<body>
    <div class="container">
        <h1>Gestión de Historias Clínicas</h1>

        <?php if (isset($_SESSION['mensaje'])): ?>
            <div class="mensaje-exito"><?= htmlspecialchars($_SESSION['mensaje']); unset($_SESSION['mensaje']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="mensaje-error"><?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <div class="form-container">
            <form method="post" action="historia_clinica.php">
                <input type="hidden" name="accion" value="registrar">
                <h2>Registrar Nueva Historia Clínica</h2>
                
                <div class="form-group">
                    <label for="id_paciente">Paciente:</label>
                    <select name="id_paciente" id="id_paciente" class="select2-paciente" required style="width: 100%;">
                        <option value="">Seleccione un paciente...</option>
                        <?php foreach ($pacientes_activos as $paciente): ?>
                            <option value="<?= $paciente['id_paciente'] ?>"><?= htmlspecialchars($paciente['nombre'] . ' ' . $paciente['apellido']) ?></option>
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

                <div class="form-group"><label>Dietas Especiales:</label><textarea name="dietas_especiales" rows="2"></textarea></div>
                <div class="form-group"><label>Observaciones Adicionales:</label><textarea name="observaciones" rows="4"></textarea></div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Crear Historia Clínica</button>
                </div>
            </form>
        </div>

        <div class="table-container">
            <h2>Historias Clínicas Registradas</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Paciente</th>
                        <th>Fecha Creación</th>
                        <th>Estado General</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($historias_clinicas)): ?>
                        <?php foreach ($historias_clinicas as $historia): ?>
                            <tr>
                                <td><?= htmlspecialchars($historia["id_historia_clinica"]) ?></td>
                                <td><?= htmlspecialchars($historia["paciente_nombre_completo"]) ?></td>
                                <td><?= htmlspecialchars($historia["fecha_formateada"]) ?></td>
                                <td title="<?= htmlspecialchars($historia["estado_salud"]) ?>"><?= htmlspecialchars(substr($historia["estado_salud"], 0, 50)) . '...' ?></td>
                                <td>
                                    <a href="editar_historia_clinica.php?id=<?= $historia['id_historia_clinica'] ?>" class="btn btn-warning">Editar/Ver</a>
                                    <a href="historia_clinica.php?idHistoriaEliminar=<?= $historia['id_historia_clinica'] ?>" class="btn btn-danger" onclick="return confirm('¿Estás seguro de que deseas eliminar esta historia?');">Eliminar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center;">No hay historias clínicas registradas.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <script>
        // Inicializa el buscador de pacientes con Select2
        $(document).ready(function() {
            $('.select2-paciente').select2({
                placeholder: "Escribe o selecciona un paciente",
                allowClear: true
            });
        });
    </script>
</body>
</html>
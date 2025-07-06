<?php
// --- INICIO DE LÓGICA PHP ---
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../../controllers/auth/verificar_sesion.php';
verificarAcceso(['Administrador']);

// Se incluyen los modelos y el controlador necesarios
require_once __DIR__ . '/../../../models/clases/historia_clinica_modelo.php';
require_once __DIR__ . '/../../../models/clases/pacientes.php';
require_once __DIR__ . '/../../../controllers/admin/HC/historia_clinica_controlador.php';

// Se determina si estamos en modo de edición o de registro
$modo_edicion = false;
$datos_historia = [];
$modelo_hc = new ModeloHistoriaClinica();

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $modo_edicion = true;
    $datos_historia = $modelo_hc->obtenerPorId((int)$_GET['id']);
    if (!$datos_historia) {
        // Si no se encuentra la historia, se redirige para evitar errores
        $_SESSION['error'] = "No se encontró la historia clínica solicitada.";
        header("Location: historia_clinica.php");
        exit();
    }
}

// Lógica para procesar el envío de formularios (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["accion"])) {
    $controlador = new ControladorHistoriaClinica();
    if ($_POST['accion'] == 'registrar') {
        $controlador->registrar();
    } elseif ($_POST['accion'] == 'actualizar') {
        $controlador->actualizar();
    }
}
// Lógica para procesar la eliminación (GET)
if (isset($_GET['idHistoriaEliminar'])) {
    $controlador = new ControladorHistoriaClinica();
    $controlador->eliminar();
}

// Se obtienen los pacientes para el dropdown, solo si no estamos en modo edición
if (!$modo_edicion) {
    $paciente_model = new Paciente();
    $pacientes_activos = $paciente_model->consultar();
}
// --- FIN DE LÓGICA PHP ---
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= $modo_edicion ? 'Editar' : 'Gestionar' ?> Historias Clínicas</title>
    <link rel="stylesheet" href="../../css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
</head>
<body>
    <div class="container">
        <h1><?= $modo_edicion ? 'Editando Historia de "' . htmlspecialchars($datos_historia['paciente_nombre_completo']) . '"' : 'Gestión de Historias Clínicas' ?></h1>

        <?php if (isset($_SESSION['mensaje'])): ?>
            <div class="mensaje-exito"><?= htmlspecialchars($_SESSION['mensaje']); unset($_SESSION['mensaje']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="mensaje-error"><?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <div class="form-container">
            <form method="post" action="historia_clinica.php">
                <input type="hidden" name="accion" value="<?= $modo_edicion ? 'actualizar' : 'registrar' ?>">
                <?php if ($modo_edicion): ?>
                    <input type="hidden" name="id_historia_clinica" value="<?= htmlspecialchars($datos_historia['id_historia_clinica']) ?>">
                <?php endif; ?>

                <h2><?= $modo_edicion ? 'Actualizar Datos de la Historia' : 'Registrar Nueva Historia Clínica' ?></h2>
                
                <div class="form-group">
                    <label>Paciente:</label>
                    <?php if ($modo_edicion): ?>
                        <input type="text" value="<?= htmlspecialchars($datos_historia['paciente_nombre_completo']) ?>" disabled>
                    <?php else: ?>
                        <select name="id_paciente" class="select2-paciente" required style="width: 100%;">
                            <option value="">Seleccione un paciente...</option>
                            <?php foreach ($pacientes_activos as $paciente): ?>
                                <option value="<?= $paciente['id_paciente'] ?>"><?= htmlspecialchars($paciente['nombre'] . ' ' . $paciente['apellido']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    <?php endif; ?>
                </div>

                <div class="form-grid">
                    <div class="form-column">
                        <div class="form-group"><label>Estado de Salud General:</label><textarea name="estado_salud" rows="3"><?= htmlspecialchars($datos_historia['estado_salud'] ?? '') ?></textarea></div>
                        <div class="form-group"><label>Antecedentes Médicos:</label><textarea name="antecedentes_medicos" rows="2"><?= htmlspecialchars($datos_historia['antecedentes_medicos'] ?? '') ?></textarea></div>
                    </div>
                    <div class="form-column">
                        <div class="form-group"><label>Condiciones Crónicas:</label><textarea name="condiciones" rows="3"><?= htmlspecialchars($datos_historia['condiciones'] ?? '') ?></textarea></div>
                        <div class="form-group"><label>Alergias Conocidas:</label><textarea name="alergias" rows="2"><?= htmlspecialchars($datos_historia['alergias'] ?? '') ?></textarea></div>
                    </div>
                </div>

                <div class="form-group"><label>Dietas Especiales:</label><textarea name="dietas_especiales" rows="2"><?= htmlspecialchars($datos_historia['dietas_especiales'] ?? '') ?></textarea></div>
                <div class="form-group"><label>Observaciones Adicionales:</label><textarea name="observaciones" rows="4"><?= htmlspecialchars($datos_historia['observaciones'] ?? '') ?></textarea></div>
                
                <div class="form-actions">
                    <?php if ($modo_edicion): ?>
                        <a href="historia_clinica.php" class="btn btn-secondary">Cancelar Edición</a>
                    <?php endif; ?>
                    <button type="submit" class="btn btn-primary"><?= $modo_edicion ? 'Actualizar Historia' : 'Crear Historia' ?></button>
                </div>
            </form>
        </div>

        <?php if (!$modo_edicion): ?>
            <div class="table-container">
                <h2>Historias Clínicas Registradas</h2>
                <div class="form-group" style="margin-bottom: 20px;">
                    <input type="search" id="buscador-historias" placeholder="Buscar por nombre o cédula del paciente..." style="width: 100%;">
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th><th>Paciente</th><th>Fecha Creación</th><th>Estado General</th><th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tabla-historias-body"></tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Activa el dropdown con buscador
            $('.select2-paciente').select2({
                placeholder: "Escribe o selecciona un paciente",
                allowClear: true
            });

            // Solo activa el buscador de la tabla si existe (es decir, si no estamos en modo edición)
            const buscador = document.getElementById('buscador-historias');
            if (buscador) {
                const tablaBody = document.getElementById('tabla-historias-body');
                let searchTimeout;

                function cargarHistorias(busqueda = '') {
                    tablaBody.innerHTML = '<tr><td colspan="5" style="text-align: center;">Buscando...</td></tr>';
                    // La URL ahora llama a la misma página para la búsqueda
                    const fetchUrl = `historia_clinica.php?accion=buscar&busqueda=${encodeURIComponent(busqueda)}`;
                    
                    fetch(fetchUrl)
                        .then(response => response.json())
                        .then(data => {
                            tablaBody.innerHTML = ''; 
                            if (data && data.length > 0) {
                                data.forEach(historia => {
                                    const estadoSalud = historia.estado_salud || '';
                                    const fila = `
                                        <tr>
                                            <td>${historia.id_historia_clinica}</td>
                                            <td>${historia.paciente_nombre_completo}</td>
                                            <td>${historia.fecha_formateada}</td>
                                            <td title="${estadoSalud}">${estadoSalud.substring(0, 50)}...</td>
                                            <td>
                                                <a href="historia_clinica.php?id=${historia.id_historia_clinica}" class="btn btn-warning">Editar</a>
                                                <a href="historia_clinica.php?idHistoriaEliminar=${historia.id_historia_clinica}" class="btn btn-danger" onclick="return confirm('¿Estás seguro?');">Eliminar</a>
                                            </td>
                                        </tr>
                                    `;
                                    tablaBody.innerHTML += fila;
                                });
                            } else {
                                tablaBody.innerHTML = '<tr><td colspan="5" style="text-align: center;">No se encontraron historias clínicas.</td></tr>';
                            }
                        });
                }

                cargarHistorias();
                buscador.addEventListener('input', () => {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => cargarHistorias(buscador.value), 400);
                });
            }
        });
    </script>
</body>
</html>
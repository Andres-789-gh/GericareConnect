<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../../controllers/auth/verificar_sesion.php';
verificarAcceso(['Administrador']);
require_once __DIR__ . "/../../../controllers/admin/HC/historia_clinica.controlador.php";

$idHistoriaClinica = isset($_GET['idHistoriaClinica']) ? (int)$_GET['idHistoriaClinica'] : 0;
if ($idHistoriaClinica === 0) die("Error: ID de historia clínica no especificado.");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_historia_clinica_editar'])) {
    ControladorHistoriaClinica::ctrEditarHistoriaClinica();
}

$historiaClinica = ControladorHistoriaClinica::ctrMostrarHistoriasClinicas('id_historia_clinica', $idHistoriaClinica);
if (!$historiaClinica) die("<h1>Error: Historia clínica con ID {$idHistoriaClinica} no encontrada.</h1>");

$modelo = new ModeloHistoriaClinica();
$medicamentosActuales = $modelo->mdlMostrarMedicamentosPorHistoria($idHistoriaClinica);
$enfermedadesActuales = $modelo->mdlMostrarEnfermedadesPorHistoria($idHistoriaClinica);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"><title>Editar Historia Clínica</title>
    <link rel="stylesheet" href="../../css/styles.css"><link rel="stylesheet" href="../../cuidador/css_cuidador/historia_clinica.css">
</head>
<body>
    <div class="container">
        <h1>Editar Historia #<?= htmlspecialchars($idHistoriaClinica) ?> de "<?= htmlspecialchars($historiaClinica['paciente_nombre_completo']); ?>"</h1>
        <div class="form-container">
            <form method="post" id="editForm" action="editar_historia_clinica.php?idHistoriaClinica=<?= $idHistoriaClinica ?>">
                <input type="hidden" name="id_historia_clinica_editar" value="<?= $idHistoriaClinica ?>">
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
                <hr>
                <div class="form-grid">
                    <div class="form-column">
                        <div class="form-group">
                            <label>Medicamentos Asignados:</label>
                            <div id="medicamentos-seleccionados" class="selection-box"></div>
                            <input type="hidden" name="medicamentos_seleccionados_ids" id="medicamentos_seleccionados_ids">
                            <button type="button" class="btn btn-add" onclick="gestionarItems('medicamento')">Gestionar Medicamentos</button>
                        </div>
                    </div>
                    <div class="form-column">
                        <div class="form-group">
                            <label>Enfermedades Diagnosticadas:</label>
                            <div id="enfermedades-seleccionadas" class="selection-box"></div>
                            <input type="hidden" name="enfermedades_seleccionadas_ids" id="enfermedades_seleccionadas_ids">
                            <button type="button" class="btn btn-add" onclick="gestionarItems('enfermedad')">Gestionar Enfermedades</button>
                        </div>
                    </div>
                </div>
                <div class="form-actions">
                    <a href="historia_clinica.php" class="btn btn-secondary">Volver al Listado</a>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const idHistoria = <?= $idHistoriaClinica ?>;
            const claveSession = `edit_page_loaded_${idHistoria}`;
            if (!sessionStorage.getItem(claveSession)) {
                localStorage.setItem('selected_medicamentos', JSON.stringify(<?= json_encode($medicamentosActuales) ?>));
                localStorage.setItem('selected_enfermedades', JSON.stringify(<?= json_encode($enfermedadesActuales) ?>));
                sessionStorage.setItem(claveSession, 'true');
            }
            cargarSelecciones();
            document.getElementById('editForm').addEventListener('submit', () => limpiarAlmacenamiento(idHistoria));
            window.addEventListener('beforeunload', (event) => {
                 if (!event.persisted) limpiarAlmacenamiento(idHistoria);
            });
        });
        function limpiarAlmacenamiento(id) {
            localStorage.removeItem('selected_medicamentos');
            localStorage.removeItem('selected_enfermedades');
            sessionStorage.removeItem(`edit_page_loaded_${id}`);
        }
        function gestionarItems(tipo) {
            window.location.href = `${tipo}.php?return_url=${encodeURIComponent(window.location.href)}`;
        }
        function cargarSelecciones() {
            actualizarVistaSeleccion('medicamentos', JSON.parse(localStorage.getItem('selected_medicamentos')) || []);
            actualizarVistaSeleccion('enfermedades', JSON.parse(localStorage.getItem('selected_enfermedades')) || []);
        }
        function actualizarVistaSeleccion(tipo, items) {
            const container = document.getElementById(`${tipo}-seleccionados`);
            const idsInput = document.getElementById(`${tipo}_seleccionados_ids`);
            if (!container || !idsInput) return;
            container.innerHTML = '';
            idsInput.value = items.map(item => {
                const tag = document.createElement('span');
                tag.className = 'selected-item';
                tag.innerHTML = `${item.nombre} <span class="remove-item" onclick="quitarItem('${tipo}', ${item.id})">×</span>`;
                container.appendChild(tag);
                return item.id;
            }).join(',');
        }
        function quitarItem(tipo, id) {
            const key = `selected_${tipo}`;
            let items = JSON.parse(localStorage.getItem(key)) || [];
            items = items.filter(item => item.id != id);
            localStorage.setItem(key, JSON.stringify(items));
            cargarSelecciones();
        }
    </script>
</body>
</html>
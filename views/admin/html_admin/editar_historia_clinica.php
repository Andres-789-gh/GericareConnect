<?php
// views/admin/html_admin/editar_historia_clinica.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../../controllers/auth/verificar_sesion.php';
verificarAcceso(['Administrador']);

require_once __DIR__ . "/../../../controllers/admin/HC/historia_clinica.controlador.php";
require_once __DIR__ . "/../../../models/clases/historia_clinica_modelo.php";

$idHistoriaClinica = isset($_GET['idHistoriaClinica']) ? (int)$_GET['idHistoriaClinica'] : 0;
$historiaClinica = null;
$medicamentosActuales = [];
$enfermedadesActuales = [];

if ($idHistoriaClinica > 0) {
    // Usamos el controlador para obtener los datos
    $historiaClinica = ControladorHistoriaClinica::ctrMostrarHistoriasClinicas('id_historia_clinica', $idHistoriaClinica);
    
    // Si la historia existe, obtenemos sus medicamentos y enfermedades asociados
    if ($historiaClinica) {
        $modelo = new ModeloHistoriaClinica();
        $medicamentosActuales = $modelo->mdlMostrarMedicamentosPorHistoria($idHistoriaClinica);
        $enfermedadesActuales = $modelo->mdlMostrarEnfermedadesPorHistoria($idHistoriaClinica);
    }
}

// Si no se encuentra la historia, detenemos la ejecución
if (!$historiaClinica) {
    echo "<h1>Error: Historia clínica no encontrada.</h1>";
    exit;
}

// Procesar el formulario de edición
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_historia_clinica_editar'])) {
    ControladorHistoriaClinica::ctrEditarHistoriaClinica();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Historia Clínica</title>
    <link rel="stylesheet" href="../../css/styles.css">
    <link rel="stylesheet" href="../../cuidador/css_cuidador/historia_clinica.css">
</head>
<body>
    <div class="container">
        <h1>Editar Historia de "<?= htmlspecialchars($historiaClinica['paciente_nombre_completo']); ?>"</h1>

        <div class="form-container">
            <form method="post" action="editar_historia_clinica.php?idHistoriaClinica=<?= $idHistoriaClinica ?>">
                <input type="hidden" name="id_historia_clinica_editar" value="<?= htmlspecialchars($idHistoriaClinica); ?>">
                
                <div class="form-grid">
                     <div class="form-column">
                        <div class="form-group">
                            <label>Estado de Salud General:</label>
                            <textarea name="estado_salud" rows="3"><?= htmlspecialchars($historiaClinica['estado_salud'] ?? ''); ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>Antecedentes Médicos:</label>
                            <textarea name="antecedentes_medicos" rows="2"><?= htmlspecialchars($historiaClinica['antecedentes_medicos'] ?? ''); ?></textarea>
                        </div>
                    </div>
                    <div class="form-column">
                        <div class="form-group">
                            <label>Condiciones Crónicas:</label>
                            <textarea name="condiciones" rows="3"><?= htmlspecialchars($historiaClinica['condiciones'] ?? ''); ?></textarea>
                        </div>
                         <div class="form-group">
                            <label>Alergias Conocidas:</label>
                            <textarea name="alergias" rows="2"><?= htmlspecialchars($historiaClinica['alergias'] ?? ''); ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Dietas Especiales:</label>
                    <textarea name="dietas_especiales" rows="2"><?= htmlspecialchars($historiaClinica['dietas_especiales'] ?? ''); ?></textarea>
                </div>
                 <div class="form-group">
                    <label>Observaciones Adicionales:</label>
                    <textarea name="observaciones" rows="4"><?= htmlspecialchars($historiaClinica['observaciones'] ?? ''); ?></textarea>
                </div>

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
                    <a href="historia_clinica.php" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Actualizar Historia</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Se ejecuta cuando la página se carga por completo
        document.addEventListener('DOMContentLoaded', () => {
            // 1. Inicializa Select2 para la búsqueda de pacientes
            $('.select2-paciente').select2({
                placeholder: "Escribe o selecciona un paciente",
                allowClear: true
            });

            // 2. Restaura el estado del formulario si existe en sessionStorage
            restaurarEstadoFormulario();

            // 3. Carga los medicamentos y enfermedades seleccionados desde localStorage
            cargarSelecciones();

            // Limpia el sessionStorage una vez que el formulario se envía con éxito para no restaurar datos viejos en un nuevo formulario.
            document.getElementById('historiaClinicaForm').addEventListener('submit', () => {
                sessionStorage.removeItem('historiaClinicaFormData');
            });
        });

        /**
         * Guarda el estado actual del formulario en sessionStorage y redirige
         * a la página de gestión de items (medicamentos/enfermedades).
         * @param {string} tipo - 'medicamento' o 'enfermedad'.
         */
        function gestionarItems(tipo) {
            const form = document.getElementById('historiaClinicaForm');
            const formData = new FormData(form);
            const formObject = Object.fromEntries(formData.entries());
            
            // Guarda los datos del formulario en sessionStorage
            sessionStorage.setItem('historiaClinicaFormData', JSON.stringify(formObject));

            // Construye la URL de retorno para que la página de gestión sepa a dónde volver
            const returnUrl = window.location.pathname + window.location.search;
            window.location.href = `${tipo}.php?return_url=${encodeURIComponent(returnUrl)}`;
        }

        /**
         * Lee los datos guardados en sessionStorage y rellena el formulario.
         */
        function restaurarEstadoFormulario() {
            const savedData = sessionStorage.getItem('historiaClinicaFormData');
            if (savedData) {
                const formData = JSON.parse(savedData);
                const form = document.getElementById('historiaClinicaForm');
                for (const key in formData) {
                    if (form.elements[key]) {
                        // Para el select de paciente, necesitamos activar Select2
                        if (key === 'id_paciente') {
                            $(form.elements[key]).val(formData[key]).trigger('change');
                        } else {
                            form.elements[key].value = formData[key];
                        }
                    }
                }
            }
        }

        // --- Las funciones para manejar localStorage se mantienen muy similares ---

        function cargarSelecciones() {
            const medicamentos = JSON.parse(localStorage.getItem('selected_medicamentos')) || [];
            const enfermedades = JSON.parse(localStorage.getItem('selected_enfermedades')) || [];
            
            actualizarVistaSeleccion('medicamentos', medicamentos);
            actualizarVistaSeleccion('enfermedades', enfermedades);
        }

        function actualizarVistaSeleccion(tipo, items) {
            const container = document.getElementById(`${tipo}-seleccionados`);
            const idsInput = document.getElementById(`${tipo}_seleccionados_ids`);
            if (!container || !idsInput) return;

            container.innerHTML = '';
            let ids = items.map(item => {
                const tag = document.createElement('span');
                tag.className = 'selected-item';
                tag.innerHTML = `${item.nombre} <span class="remove-item" onclick="quitarItem('${tipo}', ${item.id})">×</span>`;
                container.appendChild(tag);
                return item.id;
            });
            idsInput.value = ids.join(',');
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
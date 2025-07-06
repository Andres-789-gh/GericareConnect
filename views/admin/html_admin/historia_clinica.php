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
<?php
// views/cuidador/html_cuidador/historia_clinica.php
session_start();

// Incluimos los controladores y modelos necesarios para la página.
require_once __DIR__ . "/../../../controllers/cuidador/historia_clinica.controlador.php";
require_once __DIR__ . "/../../../models/clases/historia_clinica.modelo.php";
require_once __DIR__ . "/../../../models/clases/medicamento.modelo.php";
require_once __DIR__ . "/../../../models/clases/enfermedad.modelo.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['id_historia_clinica_editar'])) {
    ControladorHistoriaClinica::ctrCrearHistoriaClinica();
}

if (isset($_GET['idHistoriaClinica'])) {
    ControladorHistoriaClinica::ctrEliminarHistoriaClinica();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Historias Clínicas</title>
    <link rel="stylesheet" href="../../css/styles.css">
    <link rel="stylesheet" href="../css_cuidador/historia_clinica.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
</head>
<body>
    <div class="container">
        <h1>Gestión de Historias Clínicas</h1>

        <div class="form-container">
            <form method="post" id="historiaClinicaForm" action="historia_clinica.php">
                <h2>Registrar Nueva Historia Clínica</h2>
                
                <div class="form-grid">
                    <div class="form-column">
                        <div class="form-group">
                            <label for="id_paciente">Paciente:</label>
                            <select name="id_paciente" id="id_paciente" class="select2-paciente" required>
                                <option value="">Buscar y seleccionar un paciente</option>
                                <?php
                                $pacientes = ModeloHistoriaClinica::mdlObtenerPacientesActivos();
                                foreach ($pacientes as $paciente) {
                                    echo '<option value="' . htmlspecialchars($paciente['id_paciente']) . '">' . htmlspecialchars($paciente['nombre'] . ' ' . $paciente['apellido']) . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="estado_salud">Estado de Salud General:</label>
                            <textarea name="estado_salud" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="antecedentes_medicos">Antecedentes Médicos:</label>
                            <textarea name="antecedentes_medicos" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="form-column">
                        <div class="form-group">
                            <label for="condiciones">Condiciones Crónicas:</label>
                            <textarea name="condiciones" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="alergias">Alergias Conocidas:</label>
                            <textarea name="alergias" rows="2"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="dietas_especiales">Dietas Especiales:</label>
                            <textarea name="dietas_especiales" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="observaciones">Observaciones Adicionales:</label>
                    <textarea name="observaciones" rows="4"></textarea>
                </div>

                <div class="form-grid" style="margin-top: 20px; border-top: 1px solid #eee; padding-top: 20px;">
                    <div class="form-column">
                        <div class="form-group">
                            <label>Medicamentos Asignados:</label>
                            <div id="medicamentos-seleccionados" class="selection-box"></div>
                            <input type="hidden" name="medicamentos_seleccionados_ids" id="medicamentos_seleccionados_ids">
                            <button type="button" class="btn btn-add" onclick="abrirVentanaSeleccion('medicamento')">Seleccionar / Crear Medicamento</button>
                        </div>
                    </div>
                    <div class="form-column">
                        <div class="form-group">
                            <label>Enfermedades Diagnosticadas:</label>
                            <div id="enfermedades-seleccionadas" class="selection-box"></div>
                            <input type="hidden" name="enfermedades_seleccionadas_ids" id="enfermedades_seleccionadas_ids">
                            <button type="button" class="btn btn-add" onclick="abrirVentanaSeleccion('enfermedad')">Seleccionar / Crear Enfermedad</button>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Guardar Historia Clínica</button>
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
                        <th>Cuidador</th>
                        <th>Fecha de Consulta</th>
                        <th>Estado de Salud</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $historiasClinicas = ControladorHistoriaClinica::ctrMostrarHistoriasClinicas(null, null);

                    foreach ($historiasClinicas as $key => $value) {
                        echo '<tr>';
                        echo '<td>' . htmlspecialchars($value["id_historia_clinica"]) . '</td>';
                        echo '<td>' . htmlspecialchars($value["paciente_nombre_completo"]) . '</td>';
                        echo '<td>' . htmlspecialchars($value["cuidador_nombre_completo"]) . '</td>';
                        echo '<td>' . htmlspecialchars($value["fecha_formateada"]) . '</td>';
                        echo '<td>' . htmlspecialchars($value["estado_salud"]) . '</td>';
                        echo '<td>
                                <a href="editar_historia_clinica.php?idHistoriaClinica=' . $value["id_historia_clinica"] . '" class="btn btn-warning">Editar</a>
                                <a href="historia_clinica.php?idHistoriaClinica=' . $value["id_historia_clinica"] . '" class="btn btn-danger" onclick="return confirm(\'¿Estás seguro?\');">Eliminar</a>
                              </td>';
                        echo '</tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <script>
        $(document).ready(function() {
            $('.select2-paciente').select2({
                placeholder: "Escribe o selecciona un paciente",
                allowClear: true
            });
        });
    
        const form = document.getElementById('historiaClinicaForm');

        form.addEventListener('change', () => {
            const formData = new FormData(form);
            localStorage.setItem('historiaClinicaForm', JSON.stringify(Object.fromEntries(formData.entries())));
        });

        window.addEventListener('DOMContentLoaded', () => {
            const savedData = JSON.parse(localStorage.getItem('historiaClinicaForm'));
            if (savedData) {
                for (const key in savedData) {
                    if (key === 'id_paciente' && savedData[key]) {
                        $('#id_paciente').val(savedData[key]).trigger('change');
                    } else if (form.elements[key]) {
                        form.elements[key].value = savedData[key];
                    }
                }
            }
            cargarSelecciones();
        });

        function abrirVentanaSeleccion(tipo) {
            localStorage.setItem('historiaClinicaForm', JSON.stringify(Object.fromEntries(new FormData(form).entries())));
            window.open(`${tipo}.php?seleccionar=true`, '_blank');
        }

        function cargarSelecciones() {
            const medicamentos = JSON.parse(localStorage.getItem('selected_medicamentos')) || [];
            const enfermedades = JSON.parse(localStorage.getItem('selected_enfermedades')) || [];
            
            actualizarVistaSeleccion('medicamentos', medicamentos);
            actualizarVistaSeleccion('enfermedades', enfermedades);
        }

        function actualizarVistaSeleccion(tipo, items) {
            let containerId, idsInputId;

            if (tipo === 'medicamentos') {
                containerId = 'medicamentos-seleccionados';
                idsInputId = 'medicamentos_seleccionados_ids';
            } else if (tipo === 'enfermedades') {
                containerId = 'enfermedades-seleccionadas';
                idsInputId = 'enfermedades_seleccionadas_ids';
            } else {
                return;
            }

            const container = document.getElementById(containerId);
            const idsInput = document.getElementById(idsInputId);

            if (!container || !idsInput) return;

            container.innerHTML = '';
            let ids = [];
            if (Array.isArray(items)) {
                items.forEach(item => {
                    ids.push(item.id);
                    const tag = document.createElement('span');
                    tag.className = 'selected-item';
                    tag.innerHTML = `${item.nombre} <span class="remove-item" onclick="quitarItem('${tipo}', ${item.id})">×</span>`;
                    container.appendChild(tag);
                });
            }
            
            idsInput.value = ids.join(',');
        }

        function quitarItem(tipo, id) {
            const key = `selected_${tipo}s`;
            let items = JSON.parse(localStorage.getItem(key)) || [];
            items = items.filter(item => item.id != id);
            localStorage.setItem(key, JSON.stringify(items));
            cargarSelecciones();
        }

        window.addEventListener('focus', cargarSelecciones);
    </script>
</body>
</html>
<?php
// views/cuidador/html_cuidador/editar_historia_clinica.php
session_start();

require_once __DIR__ . "/../../../controllers/cuidador/historia_clinica.controlador.php";
require_once __DIR__ . "/../../../models/clases/historia_clinica.modelo.php";

$idHistoriaClinica = isset($_GET['idHistoriaClinica']) ? (int)$_GET['idHistoriaClinica'] : 0;
$historiaClinica = null;
$medicamentosActuales = [];
$enfermedadesActuales = [];

if ($idHistoriaClinica > 0) {
    // Obtenemos los datos principales de la historia
    $historiaClinica = ControladorHistoriaClinica::ctrMostrarHistoriasClinicas('id_historia_clinica', $idHistoriaClinica);
    // Obtenemos los medicamentos y enfermedades ya asociados
    $medicamentosActuales = ModeloHistoriaClinica::mdlMostrarMedicamentosPorHistoria($idHistoriaClinica);
    $enfermedadesActuales = ModeloHistoriaClinica::mdlMostrarEnfermedadesPorHistoria($idHistoriaClinica);
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
                    <button type="submit" class="btn btn-primary">Actualizar Historia Clínica</button>
                    <a href="historia_clinica.php" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Definición de constantes para evitar errores de tipeo
        const TIPO = {
            MEDICAMENTO: 'medicamento',
            ENFERMEDAD: 'enfermedade'
        };

        const STORAGE_KEYS = {
            MEDICAMENTOS: 'selected_medicamentos',
            ENFERMEDADES: 'selected_enfermedades'
        };

        /**
         * Abre la ventana emergente para seleccionar items.
         * @param {string} tipoSingular - 'medicamento' o 'enfermedad'.
         */
        function abrirVentanaSeleccion(tipoSingular) {
            window.open(`${tipoSingular}.php?seleccionar=true`, '_blank');
        }

        /**
         * Función central que lee el localStorage y actualiza toda la vista.
         */
        function cargarYMostrarSelecciones() {
            const medicamentos = JSON.parse(localStorage.getItem(STORAGE_KEYS.MEDICAMENTOS)) || [];
            const enfermedades = JSON.parse(localStorage.getItem(STORAGE_KEYS.ENFERMEDADES)) || [];
            
            // Actualiza la sección de medicamentos
            actualizarSeccion(TIPO.MEDICAMENTO, medicamentos);
            
            // Actualiza la sección de enfermedades
            actualizarSeccion(TIPO.ENFERMEDAD, enfermedades);
        }

        /**
         * Actualiza una sección específica (medicamentos o enfermedades) en la interfaz.
         * @param {string} tipoSingular - 'medicamento' o 'enfermedad'.
         * @param {Array} items - El array de objetos seleccionados.
         */
        function actualizarSeccion(tipoSingular, items) {
            const container = document.getElementById(`${tipoSingular}es-seleccionados`);
            const idsInput = document.getElementById(`${tipoSingular}es_seleccionados_ids`);

            if (!container || !idsInput) return;

            container.innerHTML = ''; // Limpiar la vista
            const ids = [];
            
            if (Array.isArray(items)) {
                items.forEach(item => {
                    ids.push(item.id);
                    const tag = document.createElement('span');
                    tag.className = 'selected-item';
                    tag.innerHTML = `${item.nombre} <span class="remove-item" onclick="quitarItem(event, '${tipoSingular}', ${item.id})">×</span>`;
                    container.appendChild(tag);
                });
            }
            
            idsInput.value = ids.join(','); // Actualiza el input oculto que se envía con el formulario
        }

        /**
         * Quita un item de la lista en localStorage.
         * @param {Event} event - El evento del clic.
         * @param {string} tipoSingular - 'medicamento' o 'enfermedad'.
         * @param {number} id - El ID del item a quitar.
         */
        function quitarItem(event, tipoSingular, id) {
            event.stopPropagation();
            
            // Determina la clave correcta para acceder al localStorage
            const storageKey = (tipoSingular === TIPO.MEDICAMENTO) 
                ? STORAGE_KEYS.MEDICAMENTOS 
                : STORAGE_KEYS.ENFERMEDADES;
            
            let items = JSON.parse(localStorage.getItem(storageKey)) || [];
            
            // Filtra el item a eliminar
            const nuevosItems = items.filter(item => item.id != id);
            
            // Guarda la nueva lista en localStorage
            localStorage.setItem(storageKey, JSON.stringify(nuevosItems));
            
            // Refresca toda la vista para asegurar consistencia
            cargarYMostrarSelecciones();
        }

        /**
         * Evento que se ejecuta cuando el contenido de la página se ha cargado.
         * Limpia cualquier dato antiguo y carga los datos actuales de la historia clínica.
         */
        document.addEventListener('DOMContentLoaded', () => {
            // 1. Limpia el localStorage para evitar datos de otras ediciones
            localStorage.removeItem(STORAGE_KEYS.MEDICAMENTOS);
            localStorage.removeItem(STORAGE_KEYS.ENFERMEDADES);

            // 2. Obtiene los datos actuales desde PHP
            const medicamentosActuales = <?php echo json_encode($medicamentosActuales); ?>;
            const enfermedadesActuales = <?php echo json_encode($enfermedadesActuales); ?>;
            
            // 3. Guarda los datos actuales en el localStorage
            localStorage.setItem(STORAGE_KEYS.MEDICAMENTOS, JSON.stringify(medicamentosActuales));
            localStorage.setItem(STORAGE_KEYS.ENFERMEDADES, JSON.stringify(enfermedadesActuales));

            // 4. Muestra todo en la pantalla
            cargarYMostrarSelecciones();
        });

        // Vuelve a cargar las selecciones cuando la ventana principal recupera el foco.
        window.addEventListener('focus', cargarYMostrarSelecciones);
    </script>
</body>
</html>
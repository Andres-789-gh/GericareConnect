document.addEventListener('DOMContentLoaded', function() {
    const pacienteSelect = document.getElementById('paciente_id');
    const form = document.getElementById('crear-actividad-form');
    const urlParams = new URLSearchParams(window.location.search);
    const pacienteIdPreseleccionado = urlParams.get('paciente_id');

    cargarPacientesParaSelector(pacienteSelect, pacienteIdPreseleccionado);

    if (form) {
        form.addEventListener('submit', function(event) {
            event.preventDefault();
            guardarActividad();
        });
    }
});

function cargarPacientesParaSelector(selectElement, idSeleccionado = null) {
    // Usar el script que ahora permite acceso a cuidadores y devuelve la lista completa
    fetch('admin_pacientes_obtener_lista.php')
        .then(response => {
             if (!response.ok) {
                 return response.text().then(text => { throw new Error(`Error HTTP: ${response.status} - ${text}`); });
             }
             return response.json();
         })
        .then(data => {
            selectElement.innerHTML = '<option value="">Seleccione un paciente...</option>'; // Opción por defecto

            // *** INICIO CAMBIO: Acceder a data.pacientes y manejar error ***
            if (data.error) {
                console.error("Error del servidor al cargar pacientes:", data.error);
                selectElement.innerHTML = '<option value="">Error al cargar pacientes</option>'; // Mostrar error en select
                return; // Detener ejecución si hay error
            }

            const pacientes = data.pacientes; // Obtener el array

            if (pacientes && Array.isArray(pacientes) && pacientes.length > 0) {
                pacientes.forEach(paciente => {
            // *** FIN CAMBIO ***
                    const option = document.createElement('option');
                    option.value = paciente.id;
                    option.textContent = `${paciente.nombres} ${paciente.apellidos} (${paciente.tipo_documento}: ${paciente.documento})`; // Más descriptivo
                    if (idSeleccionado && paciente.id == idSeleccionado) {
                        option.selected = true;
                    }
                    selectElement.appendChild(option);
                });
            } else if (!data.error) { // Si no hubo error pero no hay pacientes
                 selectElement.innerHTML = '<option value="">No hay pacientes registrados</option>';
            }
        })
        .catch(error => {
            console.error('Error en fetch al cargar pacientes:', error);
            selectElement.innerHTML = `<option value="">Error: ${error.message}</option>`;
        });
}
function guardarActividad() {
    const form = document.getElementById('crear-actividad-form');
    const formData = new FormData(form);
    const submitButton = form.querySelector('.submit-button');

    
    const descripcion = formData.get('descripcion');
    const pacienteId = formData.get('paciente_id');
    if (!descripcion || !pacienteId) {
        Swal.fire('Campos incompletos', 'Por favor, seleccione un paciente y escriba la descripción de la actividad.', 'warning');
        return;
    }

    submitButton.disabled = true;
    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';

    fetch('cuidador_procesar_actividad.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                title: '¡Éxito!',
                text: data.message || 'Actividad guardada correctamente.',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            });
            form.reset(); 
            
            cargarPacientesParaSelector(document.getElementById('paciente_id'));
        } else {
            Swal.fire('Error', data.message || 'No se pudo guardar la actividad.', 'error');
        }
    })
    .catch(error => {
        console.error('Error en fetch al guardar actividad:', error);
        Swal.fire('Error de Red', 'No se pudo comunicar con el servidor.', 'error');
    })
    .finally(() => {
        submitButton.disabled = false;
        submitButton.innerHTML = '<i class="fas fa-save"></i> Guardar Actividad';
    });
}
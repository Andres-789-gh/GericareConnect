document.addEventListener('DOMContentLoaded', function() {
    const pacienteList = document.getElementById('paciente-list');
    const buscarForm = document.getElementById('buscarPacientesForm');
    const buscarInput = document.getElementById('buscar-paciente');
    const clearButton = document.getElementById('clear-search-button');

    // Carga inicial de TODOS los pacientes
    cargarPacientesCuidador();

    // Event listener para el formulario de búsqueda
    if (buscarForm) {
        buscarForm.addEventListener('submit', (event) => {
            event.preventDefault();
            cargarPacientesCuidador(buscarInput.value);
        });
    }

    // Event listener para el botón de limpiar búsqueda
    if (clearButton) {
        clearButton.addEventListener('click', () => {
            buscarInput.value = '';
            cargarPacientesCuidador();
        });
    }

     // Opcional: buscar mientras se escribe (con debounce)
     if (buscarInput) {
         let searchTimeout;
         buscarInput.addEventListener('input', () => {
             clearTimeout(searchTimeout);
             searchTimeout = setTimeout(() => {
                 cargarPacientesCuidador(buscarInput.value);
             }, 400);
         });
     }

    // Event listener para clics en la lista de pacientes (delegación de eventos)
    if (pacienteList) {
        pacienteList.addEventListener('click', function(event) {
            const listItem = event.target.closest('.paciente-item[data-paciente-id]');
            if (listItem && !listItem.classList.contains('cargando') && !listItem.classList.contains('error')) {
                const pacienteId = listItem.dataset.pacienteId;
                // Abrir detalles en una ventana emergente
                const url = `detalle_paciente_cuidador.html?paciente_id=${pacienteId}`;
                const windowName = `DetallePaciente_${pacienteId}`;
                const windowFeatures = 'width=800,height=650,scrollbars=yes,resizable=yes,status=yes';
                window.open(url, windowName, windowFeatures);
            }
        });
    }
}); // Fin DOMContentLoaded


// --- Función para Cargar TODOS los Pacientes (para Cuidadores) ---
function cargarPacientesCuidador(busqueda = '') {
    const pacienteList = document.getElementById('paciente-list');
    pacienteList.innerHTML = '<li class="paciente-item cargando"><i class="fas fa-spinner fa-spin"></i> Cargando lista completa de pacientes...</li>';

    // *** LLAMADA AL SCRIPT DE ADMIN AHORA ***
    let url = 'admin_pacientes_obtener_lista.php';
    // Usar el mismo nombre de parámetro que usa el script admin
    if (busqueda) {
        url += `?buscar-paciente=${encodeURIComponent(busqueda)}`;
    }

    fetch(url)
        .then(response => {
            if (!response.ok) {
                 return response.text().then(text => {
                    throw new Error(`Error HTTP ${response.status}: ${text}`);
                 });
            }
            return response.json();
         })
        .then(data => {
            pacienteList.innerHTML = ''; // Limpiar lista

            if (data.error) { // Manejar errores devueltos por el script PHP
                throw new Error(data.error);
            }

            // *** ACCEDER A data.pacientes ***
            const pacientes = data.pacientes;

            if (pacientes && Array.isArray(pacientes) && pacientes.length > 0) {
                pacientes.forEach(paciente => {
                    const listItem = document.createElement('li');
                    listItem.classList.add('paciente-item', 'animated', 'fadeInUp');
                    listItem.dataset.pacienteId = paciente.id;
                    // El HTML del item sigue igual
                    listItem.innerHTML = `
                        <div class="paciente-info">
                            <i class="fas fa-user-circle" style="margin-right: 8px; color: #007bff;"></i> ${paciente.nombres} ${paciente.apellidos}
                            <span style="font-size: 0.85em; color: #777; margin-left: 10px;">(${paciente.tipo_documento || 'Doc'}: ${paciente.documento || 'N/A'})</span>
                        </div>
                        <i class="fas fa-chevron-right" style="color: #ccc;"></i>
                    `;
                    pacienteList.appendChild(listItem);
                });
            } else {
                // Mensaje si no hay pacientes o no se encuentran coincidencias
                pacienteList.innerHTML = '<li class="paciente-item no-data"><i class="fas fa-info-circle"></i> No hay pacientes registrados en el sistema o no se encontraron coincidencias.</li>';
            }
        })
        .catch(error => {
            console.error('Error al cargar la lista completa de pacientes para cuidador:', error);
            let errorMsg = 'Error al cargar la lista de pacientes.';
             if (error.message.includes("Usuario no autenticado")) {
                errorMsg = 'No has iniciado sesión o tu sesión ha expirado.';
             } else if (error.message.includes("Acceso no autorizado")) {
                errorMsg = 'No tienes permiso para ver esta información.';
             } else if (error.message.includes("Error HTTP")) {
                 errorMsg = `Error de comunicación con el servidor.`;
                 console.error(error.message);
             } else {
                errorMsg = `Error inesperado: ${error.message}`;
            }
            pacienteList.innerHTML = `<li class="paciente-item error"><i class="fas fa-exclamation-triangle"></i> ${errorMsg}</li>`;
        });
}
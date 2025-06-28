document.addEventListener('DOMContentLoaded', function() {
    const pacienteList = document.getElementById('paciente-list');
    const buscarForm = document.getElementById('buscarPacientesForm');
    const buscarInput = document.getElementById('buscar-paciente');
    const clearButton = document.getElementById('clear-search-button');

    // Carga inicial de pacientes y notificaciones
    cargarPacientes();

    // Event listener para el formulario de búsqueda
    if (buscarForm) {
        buscarForm.addEventListener('submit', (event) => {
            event.preventDefault();
            cargarPacientes(buscarInput.value);
        });
    }

    // Event listener para el botón de limpiar búsqueda
    if (clearButton) {
        clearButton.addEventListener('click', () => {
            buscarInput.value = '';
            cargarPacientes(); // Cargar todos los pacientes de nuevo
        });
    }

     // Opcional: buscar mientras se escribe (con debounce)
     if (buscarInput) {
         let searchTimeout;
         buscarInput.addEventListener('input', () => {
             clearTimeout(searchTimeout);
             searchTimeout = setTimeout(() => {
                 cargarPacientes(buscarInput.value);
             }, 400); // Espera 400ms después de dejar de escribir
         });
     }


    // Event listener para clics en la lista de pacientes (delegación de eventos)
    if (pacienteList) {
        pacienteList.addEventListener('click', function(event) {
            // Buscar el icono de eliminar más cercano al elemento clickeado
            const eliminarIcon = event.target.closest('.eliminar-paciente-icon');
            if (eliminarIcon) {
                event.stopPropagation(); // Evita que el clic se propague al item de la lista
                const pacienteId = eliminarIcon.dataset.pacienteId;
                // Llamar a la función que inicia el proceso de confirmación y eliminación
                confirmarYEliminarPaciente(pacienteId);
            }
            // Aquí podrías añadir lógica para otros iconos o acciones si las hubiera
            // const editarIcon = event.target.closest('.editar-paciente-icon');
            // if (editarIcon) {
            //     event.stopPropagation();
            //     const pacienteId = editarIcon.dataset.pacienteId;
            //     window.location.href = `editar_paciente.html?id=${pacienteId}`;
            // }
        });
    }
}); // Fin DOMContentLoaded


// --- Función para Cargar Pacientes ---
function cargarPacientes(busqueda = '') {
    const pacienteList = document.getElementById('paciente-list');
    pacienteList.innerHTML = '<li class="paciente-item cargando"><i class="fas fa-spinner fa-spin"></i> Cargando...</li>'; // Mensaje de carga
    let url = 'admin_pacientes_obtener_lista.php';
    if (busqueda) {
        url += `?buscar-paciente=${encodeURIComponent(busqueda)}`;
    }

    fetch(url)
        .then(response => {
            if (!response.ok) {
                 return response.text().then(text => {
                     throw new Error(`Error HTTP: ${response.status} - ${text}`);
                 });
            }
            return response.json();
        })
        .then(data => {
            pacienteList.innerHTML = ''; // Limpiar lista antes de añadir nuevos items

            if (data.error) { // Manejar error devuelto por PHP
                throw new Error(data.error);
            }

            const pacientes = data.pacientes; // Acceder al array de pacientes

            if (pacientes && Array.isArray(pacientes) && pacientes.length > 0) {
                pacientes.forEach(paciente => {
                    const listItem = document.createElement('li');
                    listItem.classList.add('paciente-item', 'animated', 'fadeInUp');
                    // Añadimos data attribute para posible uso futuro (ej. editar)
                    listItem.dataset.pacienteId = paciente.id;
                    listItem.innerHTML = `
                        <i class="fas fa-user-injured paciente-icon"></i>
                        <span class="paciente-info">
                            ${paciente.nombres} ${paciente.apellidos} - ${paciente.tipo_documento}: ${paciente.documento}
                        </span>
                        <span class="menu-icon">
                            <i class="fas fa-trash-alt eliminar-paciente-icon" data-paciente-id="${paciente.id}" title="Eliminar Paciente"></i>
                            </span>
                    `;
                    pacienteList.appendChild(listItem);
                });
                 // Actualizar badge de notificaciones si existe el conteo
                 if (typeof data.conteo_solicitudes_pendientes !== 'undefined') {
                     actualizarBadgesNotificaciones(data.conteo_solicitudes_pendientes);
                 }

            } else {
                pacienteList.innerHTML = '<li class="paciente-item no-data"><i class="fas fa-info-circle"></i> No se encontraron pacientes que coincidan.</li>';
            }
        })
        .catch(error => {
            console.error('Error al cargar los pacientes:', error);
            let errorMsg = 'Error al cargar la lista de pacientes.';
            if (error.message.includes("Acceso no autorizado")) {
                 errorMsg = 'Acceso no autorizado para ver esta lista.';
            } else if (error.message.includes("Error HTTP")) {
                 errorMsg = `Error de comunicación con el servidor.`;
                 console.error(error.message); // Log completo del error HTTP
            } else {
                errorMsg = `Error: ${error.message}`;
            }
            pacienteList.innerHTML = `<li class="paciente-item error"><i class="fas fa-exclamation-triangle"></i> ${errorMsg}</li>`;
            // Asegurarse de limpiar el badge si hay error cargando
             actualizarBadgesNotificaciones(0);
        });
}


// --- Funciones para Confirmar y Eliminar Paciente ---
function confirmarYEliminarPaciente(pacienteId) {
    Swal.fire({
        title: '¿Estás realmente seguro?',
        text: `Se eliminará el paciente con ID ${pacienteId}. Esta acción no se puede deshacer.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33', // Rojo
        cancelButtonColor: '#3085d6', // Azul
        confirmButtonText: '<i class="fas fa-trash-alt"></i> Sí, eliminar',
        cancelButtonText: '<i class="fas fa-times"></i> Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Si el usuario confirma, llamar a la función que realiza la petición de borrado
            ejecutarEliminacionPaciente(pacienteId);
        }
    });
}

function ejecutarEliminacionPaciente(pacienteId) {
     // Mostrar un indicador de carga mientras se procesa
     Swal.fire({
        title: 'Eliminando...',
        text: 'Por favor espera mientras se elimina el paciente.',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
     });

     fetch('admin_pacientes_eliminar.php', {
         method: 'POST',
         headers: {
             'Content-Type': 'application/x-www-form-urlencoded' // Tipo de contenido para enviar datos como formulario
         },
         body: `paciente_id=${pacienteId}` // Enviar el ID del paciente
     })
     .then(response => response.json()) // Esperar respuesta JSON del servidor
     .then(data => {
         if (data.success) {
             Swal.fire(
                 '¡Eliminado!',
                  data.message || 'Paciente eliminado correctamente.', // Mensaje de éxito desde PHP
                 'success'
             );
             cargarPacientes(document.getElementById('buscar-paciente')?.value || ''); // Recargar la lista
         } else {
             Swal.fire(
                 'Error al Eliminar',
                  data.message || 'No se pudo eliminar el paciente.', // Mensaje de error desde PHP
                 'error'
             );
         }
     })
     .catch(error => {
         console.error('Error en fetch al eliminar paciente:', error);
         Swal.fire(
             'Error de Red',
             'No se pudo comunicar con el servidor para eliminar el paciente. Verifica tu conexión.',
             'error'
         );
     });
}

// --- Función para Actualizar Badge de Notificaciones ---
function actualizarBadgesNotificaciones(conteoSolicitudes) {
    const badgeSolicitudes = document.getElementById('solicitudes-badge');
    if (badgeSolicitudes) {
        const conteo = parseInt(conteoSolicitudes, 10); // Asegurarse que es número
        if (!isNaN(conteo) && conteo > 0) {
            badgeSolicitudes.textContent = conteo;
            badgeSolicitudes.style.display = 'inline-block'; // Mostrar
        } else {
            badgeSolicitudes.textContent = '';
             badgeSolicitudes.style.display = 'none'; // Ocultar
        }
    }
}
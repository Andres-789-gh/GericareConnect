document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const pacienteId = urlParams.get('paciente_id');
    const nombrePacienteElem = document.getElementById('nombre-paciente');
    const actividadesListElem = document.getElementById('actividades-list');
    const sinActividadesElem = document.getElementById('sin-actividades'); // Contenedor del mensaje
    const mensajeSinActividadesTexto = document.getElementById('mensaje-sin-actividades-texto'); // Span para el texto
    const btnAgregarActividad = document.getElementById('btn-agregar-actividad');

    if (!pacienteId) {
        nombrePacienteElem.textContent = "Error: Paciente no especificado";
        actividadesListElem.innerHTML = '<li class="actividad-item error">No se proporcionó ID de paciente.</li>';
        if(sinActividadesElem) sinActividadesElem.style.display = 'none'; // Ocultar mensaje si hay error de ID
        return;
    }

    // Funcionalidad botón "Añadir Actividad" (si existe)
    if (btnAgregarActividad) {
        btnAgregarActividad.onclick = function() {
            // Intenta abrir la ventana principal si existe y no está cerrada
            if (window.opener && !window.opener.closed) {
                window.opener.location.href = `cuidador_agregar_actividad.html?paciente_id=${pacienteId}`;
                window.close(); // Cierra el popup
           } else {
                // Si no se puede acceder a la ventana principal, abre en una nueva pestaña
                window.open(`cuidador_agregar_actividad.html?paciente_id=${pacienteId}`, '_blank');
           }
        };
    }

    fetch(`obtener_detalle_paciente_cuidador.php?paciente_id=${pacienteId}`)
        .then(response => {
            if (!response.ok) {
                // Intenta obtener más detalles del error
                return response.text().then(text => { throw new Error(`Error HTTP ${response.status}: ${text}`); });
            }
            return response.json();
        })
        .then(data => {
            if (data.error) {
                throw new Error(data.error);
            }

            // Mostrar nombre del paciente
            if (data.paciente) {
                document.title = `${data.paciente.nombres} ${data.paciente.apellidos} - GeriCare`;
                nombrePacienteElem.innerHTML = `<i class="fas fa-user-circle"></i> ${data.paciente.nombres} ${data.paciente.apellidos}`;
            } else {
                nombrePacienteElem.textContent = "Paciente no encontrado";
            }

            // Mostrar lista de actividades o el mensaje personalizado
            actividadesListElem.innerHTML = ''; // Limpiar siempre la lista
            if (sinActividadesElem && mensajeSinActividadesTexto) {  // Verificar que existan los elementos del mensaje
                if (data.actividades && data.actividades.length > 0) {
                    sinActividadesElem.style.display = 'none'; // Ocultar mensaje si hay actividades
                    data.actividades.forEach(act => {
                        const listItem = document.createElement('li');
                        listItem.classList.add('actividad-item');

                        let fechaHtml = '';
                        // *** CAMBIO: Solo mostrar fecha si existe ***
                        if (act.fecha_programada_f) {
                            fechaHtml = `<span class="actividad-fecha-hora"><i class="far fa-calendar-alt"></i> ${act.fecha_programada_f}</span>`;
                        }

                        // *** CAMBIO: Se quita la referencia a hora_programada_f ***
                        listItem.innerHTML = `
                            <span class="actividad-descripcion"><strong>${act.descripcion || 'Sin descripción'}</strong></span>
                            ${fechaHtml}
                            <span class="actividad-estado estado-${(act.estado || 'pendiente').toLowerCase()}">${act.estado || 'Pendiente'}</span>
                        `;
                        actividadesListElem.appendChild(listItem);
                    });
                } else {
                    // *** CAMBIO: Mostrar mensaje personalizado ***
                    mensajeSinActividadesTexto.textContent = 'Este paciente no tiene actividades asignadas.'; // Tu mensaje personalizado
                    sinActividadesElem.style.display = 'block'; // Mostrar el contenedor del mensaje
                    actividadesListElem.innerHTML = ''; // Asegurar que la lista UL esté vacía
                }
            } else {
                console.error("Elementos 'sin-actividades' o 'mensaje-sin-actividades-texto' no encontrados en el HTML.");
                // Mostrar actividades si el mensaje no se puede mostrar
                if (data.actividades && data.actividades.length > 0) {
                    data.actividades.forEach(act => {
                        const listItem = document.createElement('li');
                        listItem.classList.add('actividad-item');
                        let fechaHtml = '';
                        if (act.fecha_programada_f) {
                            fechaHtml = `<span class="actividad-fecha-hora"><i class="far fa-calendar-alt"></i> ${act.fecha_programada_f}</span>`;
                        }
                        listItem.innerHTML = `
                            <span class="actividad-descripcion"><strong>${act.descripcion || 'Sin descripción'}</strong></span>
                            ${fechaHtml}
                            <span class="actividad-estado estado-${(act.estado || 'pendiente').toLowerCase()}">${act.estado || 'Pendiente'}</span>
                        `;
                        actividadesListElem.appendChild(listItem);
                    });
                } else {
                    actividadesListElem.innerHTML = '<li class="actividad-item">No hay actividades asignadas.</li>'; // Mensaje genérico
                }
            }
        })
        .catch(error => {
            console.error('Error al cargar detalles del paciente:', error);
            nombrePacienteElem.textContent = "Error al cargar datos";
            actividadesListElem.innerHTML = `<li class="actividad-item error"><i class="fas fa-exclamation-triangle"></i> Error: ${error.message}</li>`;
            if(sinActividadesElem) sinActividadesElem.style.display = 'none'; // Ocultar mensaje si hay error
        });
});
function mostrarModalDetalle(solicitud) {
    console.log("Datos completos de la solicitud para modal:", solicitud);

    const estadoActual = solicitud.estado || 'Pendiente';
    const estadoClass = `estado-${estadoActual.toLowerCase().replace(/\s+/g, '-')}`;
    let contenidoHtml = `
    <div class="detalle-solicitud-contenido">
        <p><strong>ID:</strong> ${solicitud.id} &nbsp;&nbsp; <strong>Tipo:</strong> ${solicitud.tipo_solicitud}</p>
        <p><strong>Fecha:</strong> ${solicitud.fecha_formateada} &nbsp;&nbsp; <strong>Estado:</strong> <span class="estado-base ${estadoClass}">${estadoActual}</span></p>
        <p><strong>Enviada por:</strong> ${solicitud.familiar_nombres || '?'} ${solicitud.familiar_apellidos || '?'} (<a href="mailto:${solicitud.familiar_correo || ''}">${solicitud.familiar_correo || '?'}</a>)</p>
        <h4><i class="far fa-file-alt"></i> Motivo/Detalles Enviados:</h4>
        <pre>${solicitud.descripcion || '(Sin descripción adicional)'}</pre>
    `;

    let datosParaAgregar = null;
    if (solicitud.tipo_solicitud === 'Ingreso' && solicitud.datos_paciente_nuevo && typeof solicitud.datos_paciente_nuevo === 'object') {
        console.log("Datos paciente nuevo (objeto):", solicitud.datos_paciente_nuevo);

        const familiarData = solicitud.datos_paciente_nuevo.familiar || {};
        const pacienteData = solicitud.datos_paciente_nuevo.paciente || {};
        datosParaAgregar = {
            solicitud_id: solicitud.id,
            familiar_id: solicitud.usuario_id,
            paciente: {
                nombres: pacienteData.nombres || '',
                apellidos: pacienteData.apellidos || '',
                cc: pacienteData.cc || ''
            }
        };

        contenidoHtml += `
            <h4><i class="fas fa-user-friends"></i> Datos Adicionales (Ingreso)</h4>
            <p><strong>Familiar:</strong> ${familiarData.nombres || '?'} ${familiarData.apellidos || '?'} (CC: ${familiarData.cc || '?'}, Email: ${familiarData.email || '?'})</p>
            <p><strong>Paciente:</strong> ${pacienteData.nombres || '?'} ${pacienteData.apellidos || '?'} (CC: ${pacienteData.cc || '?'})</p>
        `;
    } else if (solicitud.tipo_solicitud === 'Ingreso') {
         console.warn("Solicitud de Ingreso sin datos_paciente_nuevo válidos:", solicitud.datos_paciente_nuevo);
         contenidoHtml += `<h4><i class="fas fa-user-friends"></i> Datos Adicionales (Ingreso)</h4><p>(No se encontraron datos adicionales válidos en esta solicitud)</p>`;
    }


    if (solicitud.tipo_solicitud === 'Retiro' && solicitud.paciente_id_relacionado) {
         contenidoHtml += `
            <h4><i class="fas fa-user-minus"></i> Paciente a Retirar</h4>
            <p>ID: ${solicitud.paciente_id_relacionado} ${solicitud.paciente_relacionado_nombre_completo ? ` - <strong>${solicitud.paciente_relacionado_nombre_completo}</strong>` : '(Nombre no disponible)'}</p>
         `;
    }

    contenidoHtml += `
        <hr>
        <h4><i class="fas fa-user-shield"></i> Respuesta del Administrador</h4>
        <div class="respuesta-admin-area">
            <textarea id="swal-respuesta-admin" placeholder="Escriba la respuesta para el familiar aquí...">${solicitud.respuesta_admin || ''}</textarea>
        </div>
    </div>`;

    const swalConfig = {
        title: `Detalle Solicitud #${solicitud.id}`,
        html: contenidoHtml,
        showCancelButton: true,
        cancelButtonText: 'Cerrar',
        showDenyButton: estadoActual !== 'Rechazada' && estadoActual !== 'Completada',
        denyButtonText: '<i class="fas fa-times-circle"></i> Rechazar Solicitud',
        confirmButtonColor: '#28a745',
        denyButtonColor: '#dc3545',
        width: '750px',
        footer: generarBotonesFooterModal(solicitud),
        customClass: { actions: 'swal-actions-custom', footer: 'swal-footer-custom' },
        preDeny: () => {
             const respuesta = document.getElementById('swal-respuesta-admin').value;
             if (!respuesta && estadoActual !== 'Rechazada') {
                  Swal.showValidationMessage('Se requiere una respuesta/motivo para rechazar.');
                  return false;
             }
              return { accion: 'denegar', estado: 'Rechazada', respuesta: respuesta };
         }
    };


    if (solicitud.tipo_solicitud === 'Ingreso' && datosParaAgregar && estadoActual === 'Pendiente') {
        swalConfig.showConfirmButton = true;
        swalConfig.confirmButtonText = '<i class="fas fa-user-plus"></i> Agregar Paciente';
        swalConfig.preConfirm = () => {
            return { accion: 'agregar_paciente', datos: datosParaAgregar };
        };
    } else if (solicitud.tipo_solicitud === 'Retiro' && estadoActual === 'Pendiente') {
        swalConfig.showConfirmButton = true;
        swalConfig.confirmButtonText = '<i class="fas fa-check-circle"></i> Procesar Retiro';
         swalConfig.preConfirm = () => {
             const respuesta = document.getElementById('swal-respuesta-admin').value;
             return { accion: 'confirmar', estado: 'Procesada', respuesta: respuesta };
         };
    } else if (solicitud.tipo_solicitud !== 'Ingreso' && solicitud.tipo_solicitud !== 'Retiro' && estadoActual === 'Pendiente') {
        swalConfig.showConfirmButton = true;
        swalConfig.confirmButtonText = '<i class="fas fa-check"></i> Aprobar Solicitud';
         swalConfig.preConfirm = () => {
             const respuesta = document.getElementById('swal-respuesta-admin').value;
             return { accion: 'confirmar', estado: 'Aprobada', respuesta: respuesta };
         };
    } else {
        swalConfig.showConfirmButton = false;
    }


    Swal.fire(swalConfig).then((result) => {
         if (result.isConfirmed) {
            const { accion, datos, estado, respuesta } = result.value;
             if (accion === 'agregar_paciente') {
                 irAgregarPaciente(datos);
             } else if (accion === 'confirmar') {
                  actualizarEstadoSolicitud(solicitud.id, estado, respuesta);
                 if (solicitud.tipo_solicitud === 'Retiro' && estado === 'Procesada' && solicitud.paciente_id_relacionado) {

                 }
             }
         } else if (result.isDenied) {
             const { estado, respuesta } = result.value;
             actualizarEstadoSolicitud(solicitud.id, estado, respuesta);
         } else if (result.isDismissed && document.getElementById('swal-respuesta-admin')) {
            const respuesta = document.getElementById('swal-respuesta-admin').value;
             if (respuesta !== (solicitud.respuesta_admin || '') && (estadoActual === 'Pendiente' || estadoActual === 'Aprobada' || estadoActual === 'Procesada')) {
                Swal.fire({
                    title: '¿Guardar cambios en la respuesta?',
                    text: "Modificaste la respuesta pero no cambiaste el estado. ¿Guardar este texto?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Guardar Respuesta',
                    cancelButtonText: 'Descartar Cambios',
                    confirmButtonColor: '#007bff'
                }).then((saveResult) => {
                    if (saveResult.isConfirmed) {
                         actualizarEstadoSolicitud(solicitud.id, estadoActual, respuesta, true);
                    }
                });
             }
         }
    });
}

function irAgregarPaciente(datos) {
    if (!datos || !datos.paciente) {
        console.error("Faltan datos para redirigir a agregar paciente.");
        Swal.fire('Error', 'Faltan datos para poder agregar al paciente desde esta solicitud.', 'error');
        return;
    }
    const datosCodificados = encodeURIComponent(JSON.stringify(datos));
    const url = `agregar_paciente.html?data=${datosCodificados}`;
    window.open(url, '_blank');
}


document.addEventListener('DOMContentLoaded', function() {
    const solicitudList = document.getElementById('solicitud-list');
    const buscarForm = document.getElementById('buscarSolicitudesForm');
    const buscarInput = document.getElementById('buscar-solicitud');
    const clearButton = document.getElementById('clear-search-button');


    cargarSolicitudesAdmin();


    if (buscarForm) {
        buscarForm.addEventListener('submit', (event) => {
            event.preventDefault();
            cargarSolicitudesAdmin(buscarInput.value);
        });
    }

    if (clearButton) {
        clearButton.addEventListener('click', () => {
            buscarInput.value = '';
            cargarSolicitudesAdmin();
        });
    }

    if (solicitudList) {
        solicitudList.addEventListener('click', function(event) {
            const eliminarIcon = event.target.closest('.eliminar-solicitud-icon');
            const listItem = event.target.closest('.solicitud-item[data-solicitud-id]');

            if (eliminarIcon) {
                event.stopPropagation(); 
                const solicitudId = eliminarIcon.dataset.solicitudId;
                confirmarYEliminarSolicitud(solicitudId);
            } else if (listItem && !listItem.classList.contains('cargando') && !listItem.classList.contains('error')) {
                const solicitudId = listItem.dataset.solicitudId;
                const solicitudData = JSON.parse(listItem.dataset.solicitudCompleta || '{}');
                if (solicitudData && solicitudData.id) {
                     mostrarModalDetalle(solicitudData);
                } else {
                     console.error("No se pudieron obtener los datos completos de la solicitud desde el dataset.");
                     Swal.fire('Error', 'No se pudo cargar el detalle completo de la solicitud.', 'error');
                 }
            }
        });
    }
});

function cargarSolicitudesAdmin(busqueda = '') {
    const solicitudList = document.getElementById('solicitud-list');
    solicitudList.innerHTML = '<li class="solicitud-item cargando"><i class="fas fa-spinner fa-spin"></i> Cargando solicitudes...</li>';
    let url = 'admin_solicitudes_obtener.php';
    if (busqueda) {
        url += `?buscar=${encodeURIComponent(busqueda)}`;
    }

    fetch(url)
        .then(response => {
            if (!response.ok) throw new Error(`Error HTTP: ${response.status}`);
            return response.json();
        })
        .then(data => {
            solicitudList.innerHTML = '';
            if (data.error) throw new Error(data.error);

            if (data.solicitudes && data.solicitudes.length > 0) {
                data.solicitudes.forEach(solicitud => {
                    const listItem = document.createElement('li');
                    listItem.classList.add('solicitud-item', 'animated', 'fadeInUp');
                    listItem.dataset.solicitudId = solicitud.id;
                    listItem.dataset.tipo = solicitud.tipo_solicitud;

                    listItem.dataset.solicitudCompleta = JSON.stringify(solicitud);

                    const estadoActual = solicitud.estado || 'Pendiente';
                    const estadoClass = `estado-${estadoActual.toLowerCase().replace(/\s+/g, '-')}`;

                    listItem.innerHTML = `
                        <div class="solicitud-col-principal">
                            <i class="solicitud-icono ${obtenerIconoTipoAdmin(solicitud.tipo_solicitud)}"></i>
                            <div>
                                <strong>${solicitud.tipo_solicitud} #${solicitud.id}</strong>
                                <span class="solicitud-fecha">${solicitud.fecha_formateada || 'Fecha desconocida'}</span>
                            </div>
                        </div>
                        <div class="solicitud-col-familiar">
                             ${solicitud.familiar_nombres || '?'} ${solicitud.familiar_apellidos || '?'}
                             ${solicitud.familiar_correo ? `<a href="mailto:${solicitud.familiar_correo}" class="solicitud-email" onclick="event.stopPropagation();">${solicitud.familiar_correo}</a>` : ''}
                        </div>
                        <div class="solicitud-col-estado">
                             <span class="estado-base ${estadoClass}">${estadoActual}</span>
                        </div>
                        <div class="solicitud-col-acciones">
                            <i class="fas fa-trash-alt eliminar-solicitud-icon" data-solicitud-id="${solicitud.id}" title="Eliminar Solicitud"></i>
                        </div>
                    `;
                    solicitudList.appendChild(listItem);
                });
            } else {
                solicitudList.innerHTML = '<li class="solicitud-item no-data"><i class="fas fa-info-circle"></i> No se encontraron solicitudes.</li>';
            }
        })
        .catch(error => {
            console.error('Error al cargar las solicitudes:', error);
            solicitudList.innerHTML = `<li class="solicitud-item error"><i class="fas fa-exclamation-triangle"></i> Error al cargar solicitudes: ${error.message}</li>`;
        });
}

function obtenerIconoTipoAdmin(tipo) {
    switch(tipo) {
        case 'Ingreso': return 'fas fa-user-plus';
        case 'Retiro': return 'fas fa-user-minus';
        case 'Consulta': return 'fas fa-question-circle';
        default: return 'fas fa-envelope';
    }
}


function generarBotonesFooterModal(solicitud) {
    let buttonsHtml = '';
    const estado = solicitud.estado || 'Pendiente';


    if (estado === 'Aprobada' && solicitud.tipo_solicitud === 'Ingreso') {
        buttonsHtml += `
            <button type="button" class="swal2-styled swal-button swal-button-agregar" onclick='irAgregarPaciente(${JSON.stringify({solicitud_id: solicitud.id, familiar_id: solicitud.usuario_id, paciente: solicitud.datos_paciente_nuevo?.paciente || {}})})'>
                <i class="fas fa-user-plus"></i> Ir a Agregar Paciente
            </button>`;
    }
    if (estado === 'Procesada' && solicitud.tipo_solicitud === 'Retiro' && solicitud.paciente_id_relacionado) {
         buttonsHtml += `
            <button type="button" class="swal2-styled swal-button swal-button-eliminar" onclick='confirmarYEliminarPaciente(${solicitud.paciente_id_relacionado}, ${solicitud.id})'>
                <i class="fas fa-trash-alt"></i> Confirmar Eliminación Paciente
            </button>`;
    }

    if (estado === 'Pendiente' || estado === 'Aprobada' || estado === 'Procesada' || estado === 'Rechazada') {
        const respuestaActual = document.getElementById('swal-respuesta-admin') ? document.getElementById('swal-respuesta-admin').value : (solicitud.respuesta_admin || '');
         if(respuestaActual !== (solicitud.respuesta_admin || '')) {
             buttonsHtml += `
                 <button type="button" class="swal2-styled swal-button swal-button-responder" onclick="actualizarEstadoSolicitud(${solicitud.id}, '${estado}', document.getElementById('swal-respuesta-admin').value, true)">
                      <i class="fas fa-save"></i> Guardar Respuesta
                  </button>`;
         }
    }


    return buttonsHtml || '<span></span>';
}


function actualizarEstadoSolicitud(solicitudId, nuevoEstado, respuesta, soloGuardarRespuesta = false) {
     Swal.showLoading();
     const formData = new FormData();
     formData.append('solicitud_id', solicitudId);
     formData.append('estado', nuevoEstado);
     formData.append('respuesta', respuesta || '');

     fetch('admin_actualizar_solicitud.php', {
         method: 'POST',
         body: formData
     })
     .then(response => response.json())
     .then(data => {
         if (data.success) {
             Swal.fire({
                 title: soloGuardarRespuesta ? 'Respuesta Guardada' : '¡Actualizado!',
                 text: data.message || `La solicitud #${solicitudId} ha sido actualizada a ${nuevoEstado}.`,
                 icon: 'success',
                 timer: soloGuardarRespuesta ? 1500 : 2000, 
                 showConfirmButton: false
             });
             cargarSolicitudesAdmin(document.getElementById('buscar-solicitud')?.value || '');
             if (!soloGuardarRespuesta) {
                 const currentSwal = Swal.getHtmlContainer();
                 if (currentSwal && currentSwal.closest('.swal2-popup').querySelector('.swal2-title').textContent.includes(`#${solicitudId}`)) {

                 } else {
                    Swal.close();
                 }
            }

         } else {
             throw new Error(data.message || 'Error desconocido al actualizar.');
         }
     })
     .catch(error => {
         console.error("Error al actualizar estado:", error);
         Swal.fire(
             'Error',
             `No se pudo actualizar la solicitud: ${error.message}`,
             'error'
         );
     });
}

function confirmarYEliminarSolicitud(solicitudId) {
     Swal.fire({
        title: '¿Estás seguro?',
        text: `¿Realmente deseas eliminar la solicitud con ID ${solicitudId}? Esta acción no se puede deshacer.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
     }).then((result) => {
         if (result.isConfirmed) {
             ejecutarEliminacionSolicitud(solicitudId);
         }
     });
 }

 function ejecutarEliminacionSolicitud(solicitudId) {
     Swal.fire({title: 'Eliminando...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); }});

     fetch('admin_solicitudes_eliminar.php', {
         method: 'POST',
         headers: {'Content-Type': 'application/x-www-form-urlencoded'},
         body: `solicitud_id=${solicitudId}`
     })
     .then(response => response.json())
     .then(data => {
         Swal.close();
         if (data.success) {
             Swal.fire('¡Eliminada!', data.message || 'La solicitud ha sido eliminada.', 'success');
             cargarSolicitudesAdmin(document.getElementById('buscar-solicitud')?.value || ''); 
         } else {
             Swal.fire('Error', data.message || 'No se pudo eliminar la solicitud.', 'error');
         }
     })
     .catch(error => {
          Swal.close();
          console.error("Error fetch eliminar solicitud:", error);
          Swal.fire('Error de Red', 'No se pudo comunicar con el servidor para eliminar la solicitud.', 'error');
      });
 }
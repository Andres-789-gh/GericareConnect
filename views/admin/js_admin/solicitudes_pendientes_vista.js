document.addEventListener('DOMContentLoaded', function() {
    const solicitudList = document.getElementById('solicitud-list');


    cargarHistorialSolicitudes();


    if (solicitudList) {
        solicitudList.addEventListener('click', function(event) {

            const listItem = event.target.closest('.solicitud-item[data-solicitud-id]');
            if (listItem && !listItem.classList.contains('cargando') && !listItem.classList.contains('error')) {
                const solicitudId = listItem.dataset.solicitudId;
                const tipoSolicitud = listItem.dataset.tipoSolicitud;
                const estadoSolicitud = listItem.dataset.estadoSolicitud;
                const descripcion = listItem.dataset.descripcion;
                const respuestaAdmin = listItem.dataset.respuestaAdmin; 
                const fechaFormateada = listItem.dataset.fechaFormateada;


                if (tipoSolicitud === 'Ingreso' && estadoSolicitud === 'Aprobada') {

                     Swal.fire({
                        title: 'Solicitud Aprobada',
                        text: "Tu solicitud de ingreso ha sido aprobada. Serás redirigido a la lista de familiares.",
                        icon: 'success',
                        timer: 3000, 
                        showConfirmButton: false,
                        willClose: () => {
                            window.location.href = `familiares.html?ingreso_aprobado=${solicitudId}`;
                        }
                    });

                } else {

                    let contenidoHtml = `
                        <div style="text-align: left;">
                            <p><strong>Tipo:</strong> ${tipoSolicitud}</p>
                            <p><strong>Fecha:</strong> ${fechaFormateada}</p>
                            <p><strong>Estado:</strong> <span class="estado-base estado-${estadoSolicitud.toLowerCase().replace(/\s+/g, '-')}">${estadoSolicitud}</span></p>
                            <hr>
                            <h4><i class="far fa-file-alt"></i> Motivo/Detalles Enviados:</h4>
                            <pre style="white-space: pre-wrap; background-color: #f8f9fa; padding: 10px; border-radius: 4px; border: 1px solid #eee;">${descripcion || 'No especificado'}</pre>
                            <hr>
                            <h4><i class="fas fa-user-shield"></i> Respuesta del Administrador:</h4>
                    `;
                    if (respuestaAdmin && respuestaAdmin !== 'null' && respuestaAdmin.trim() !== '') {
                         contenidoHtml += `<div class="respuesta-admin" style="background-color: #e9ecef; padding: 10px; border-left: 3px solid #007bff; border-radius: 4px;">${respuestaAdmin}</div>`;
                    } else {
                         contenidoHtml += `<p class="no-respuesta" style="font-style: italic; color: #6c757d;">Aún no hay respuesta.</p>`;
                    }
                    contenidoHtml += `</div>`;

                    Swal.fire({
                        title: 'Detalle de la Solicitud',
                        html: contenidoHtml,
                        icon: obtenerIconoEstado(estadoSolicitud),
                        confirmButtonText: 'Cerrar',
                        width: '600px'
                    });
                }
            }
        });
    }
});

function cargarHistorialSolicitudes(busqueda = '') {
    const solicitudList = document.getElementById('solicitud-list');
    solicitudList.innerHTML = '<li class="solicitud-item cargando"><i class="fas fa-spinner fa-spin"></i> Cargando historial...</li>';

    let url = `obtener_historial_solicitudes.php`;
    if (busqueda) {
        url += `?buscar=${encodeURIComponent(busqueda)}`;
    }

    fetch(url)
        .then(response => {
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
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
                    listItem.dataset.tipoSolicitud = solicitud.tipo_solicitud;
                    listItem.dataset.estadoSolicitud = solicitud.estado || 'Pendiente';
                    listItem.dataset.descripcion = solicitud.descripcion;
                    listItem.dataset.respuestaAdmin = solicitud.respuesta_admin; 
                    listItem.dataset.fechaFormateada = solicitud.fecha_formateada;


                    const estadoClass = `estado-${(solicitud.estado || 'pendiente').toLowerCase().replace(' ', '-')}`;
                    const tieneRespuesta = solicitud.respuesta_admin && solicitud.respuesta_admin.trim() !== '';

                    listItem.innerHTML = `
                        <div class="solicitud-info">
                             <i class="${obtenerIconoTipo(solicitud.tipo_solicitud)}"></i>
                            <strong>${solicitud.tipo_solicitud}</strong>
                            <span>${solicitud.fecha_formateada}</span>
                            <span class="estado-base ${estadoClass}">${solicitud.estado || 'Pendiente'}</span>
                        </div>
                         ${tieneRespuesta ? '<i class="fas fa-comment-dots" title="Tiene respuesta" style="color: #007bff; margin-left: 10px;"></i>' : ''}
                    `;
                    solicitudList.appendChild(listItem);
                });
            } else {
                solicitudList.innerHTML = '<li class="solicitud-item no-data"><i class="fas fa-info-circle"></i> No has enviado ninguna solicitud aún.</li>';
            }
        })
        .catch(error => {
            console.error('Error al cargar el historial:', error);
            solicitudList.innerHTML = `<li class="solicitud-item error"><i class="fas fa-exclamation-triangle"></i> Error al cargar historial: ${error.message}</li>`;
        });
}

function obtenerIconoTipo(tipoSolicitud) {
    switch (tipoSolicitud) {
        case 'Ingreso': return 'fas fa-user-plus';
        case 'Retiro': return 'fas fa-user-minus';
        case 'Consulta': return 'fas fa-question-circle';
        default: return 'fas fa-envelope';
    }
}
function obtenerIconoEstado(estado) {
     switch ((estado || 'Pendiente').toLowerCase()) {
        case 'aprobada': return 'success';
        case 'rechazada': return 'error';
        case 'pendiente': return 'warning';
        case 'procesada': return 'info';
        case 'completada': return 'success';
        default: return 'info';
    }
}
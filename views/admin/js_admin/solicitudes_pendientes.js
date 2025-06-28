document.addEventListener('DOMContentLoaded', function() {
    const solicitudList = document.getElementById('solicitud-list');

    // Carga inicial del historial
    cargarHistorialSolicitudes();

    // Event listener para clics en la lista (delegación)
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

                // Lógica específica para 'Ingreso' Aprobada
                if (tipoSolicitud === 'Ingreso' && estadoSolicitud === 'Aprobada') {
                    // Redireccionar a familiares.html (quizás añadiendo un parámetro)
                     Swal.fire({
                        title: 'Solicitud Aprobada',
                        text: "Tu solicitud de ingreso ha sido aprobada. Serás redirigido a la lista de familiares.",
                        icon: 'success',
                        timer: 3000, // Duración antes de redirigir
                        showConfirmButton: false,
                        willClose: () => {
                            window.location.href = `familiares.html?ingreso_aprobado=${solicitudId}`;
                        }
                    });

                } else {
                    // Para otras solicitudes o estados, mostrar detalles en un modal
                    let contenidoHtml = `
                        <p><strong>Tipo:</strong> ${tipoSolicitud}</p>
                        <p><strong>Fecha:</strong> ${fechaFormateada}</p>
                        <p><strong>Estado:</strong> <span class="estado-${estadoSolicitud.toLowerCase()}">${estadoSolicitud}</span></p>
                        <p><strong>Motivo/Detalles:</strong></p>
                        <p style="white-space: pre-wrap;">${descripcion || 'No especificado'}</p>
                        <hr>
                        <p><strong>Respuesta del Administrador:</strong></p>
                    `;
                    if (respuestaAdmin && respuestaAdmin !== 'null') { // Verificar si hay respuesta real
                         contenidoHtml += `<div class="respuesta-admin">${respuestaAdmin}</div>`;
                    } else {
                         contenidoHtml += `<p class="no-respuesta">Aún no hay respuesta.</p>`;
                    }


                    Swal.fire({
                        title: 'Detalle de la Solicitud',
                        html: contenidoHtml,
                        icon: obtenerIconoEstado(estadoSolicitud),
                        confirmButtonText: 'Cerrar',
                         width: '600px' // Ancho del modal
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
            solicitudList.innerHTML = ''; // Limpiar lista
            if (data.error) throw new Error(data.error);

            if (data.solicitudes && data.solicitudes.length > 0) {
                data.solicitudes.forEach(solicitud => {
                    const listItem = document.createElement('li');
                    listItem.classList.add('solicitud-item', 'animated', 'fadeInUp');
                    // Guardar datos relevantes en atributos data-*
                    listItem.dataset.solicitudId = solicitud.id;
                    listItem.dataset.tipoSolicitud = solicitud.tipo_solicitud;
                    listItem.dataset.estadoSolicitud = solicitud.estado || 'Pendiente'; // Estado por defecto
                    listItem.dataset.descripcion = solicitud.descripcion;
                    listItem.dataset.respuestaAdmin = solicitud.respuesta_admin;
                    listItem.dataset.fechaFormateada = solicitud.fecha_formateada;

                     // Clase CSS basada en el estado para estilo visual
                    const estadoClass = `estado-${(solicitud.estado || 'pendiente').toLowerCase().replace(' ', '-')}`;

                    listItem.innerHTML = `
                        <div class="solicitud-info">
                             <i class="${obtenerIconoTipo(solicitud.tipo_solicitud)}"></i>
                            <strong>${solicitud.tipo_solicitud}</strong>
                            <span>${solicitud.fecha_formateada}</span>
                            <span class="estado ${estadoClass}">${solicitud.estado || 'Pendiente'}</span>
                        </div>
                         ${solicitud.respuesta_admin ? '<i class="fas fa-comment-dots" title="Tiene respuesta"></i>' : ''}

                    `;
                    solicitudList.appendChild(listItem);
                });
            } else {
                solicitudList.innerHTML = '<li class="solicitud-item" id="no-solicitudes"><i class="fas fa-info-circle"></i> No has enviado ninguna solicitud aún.</li>';
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
        default: return 'info';
    }
}
document.addEventListener('DOMContentLoaded', function() {
    cargarSolicitudes();

    const buscarForm = document.getElementById('buscarSolicitudesForm');
    const buscarSolicitudInput = document.getElementById('buscar-solicitud');
    const solicitudList = document.getElementById('solicitud-list');

    buscarForm.addEventListener('submit', function(event) {
        event.preventDefault();
        cargarSolicitudes(buscarSolicitudInput.value);
    });

    buscarSolicitudInput.addEventListener('input', function() {
    });

    solicitudList.addEventListener('click', function(event) {
        const listItem = event.target.closest('.solicitud-item');
        if (listItem) {
            const solicitudId = listItem.dataset.solicitudId;
            const solicitudTipo = listItem.dataset.solicitudTipo;
            const solicitudDescripcion = listItem.dataset.solicitudDescripcion;

            let mensaje = `Solicitud de tipo: ${solicitudTipo}\nID: ${solicitudId}\n\nMotivo:\n${solicitudDescripcion}`;

            if (solicitudTipo === 'Ingreso') {
                mensaje += `\n\nPara agregar a este paciente, haga clic en el siguiente enlace:\n<a href="agregar_paciente.html?solicitud_id=${solicitudId}" target="_blank">Agregar Paciente</a>`;
            }

            const mensajeDiv = document.createElement('div');
            mensajeDiv.innerHTML = mensaje.replace(/\n/g, '<br>');

            Swal.fire({
                title: 'Detalle de la Solicitud',
                html: mensajeDiv.outerHTML,
                confirmButtonText: 'Cerrar'
            });
        }
    });
});

function cargarSolicitudes(busqueda = '') {
    const solicitudList = document.getElementById('solicitud-list');
    solicitudList.innerHTML = '<li class="solicitud-item cargando">Cargando solicitudes...</li>';

    let url = 'admin_solicitudes_obtener.php';
    if (busqueda) {
        url += `?buscar-solicitud=${encodeURIComponent(busqueda)}`;
    }

    fetch(url)
        .then(response => response.json())
        .then(data => {
            solicitudList.innerHTML = '';
            if (data && data.length > 0) {
                data.forEach(solicitud => {
                    const listItem = document.createElement('li');
                    listItem.classList.add('solicitud-item', 'animated', 'fadeInUp');
                    listItem.dataset.solicitudId = solicitud.id;
                    listItem.dataset.solicitudTipo = solicitud.tipo_solicitud;
                    listItem.dataset.solicitudDescripcion = solicitud.descripcion;
                    listItem.innerHTML = `
                        <strong>${solicitud.tipo_solicitud}</strong> (${solicitud.estado || 'Pendiente'}) - ${new Date(solicitud.fecha_creacion).toLocaleDateString()}
                        <span class="solicitud-details"></span>
                    `;
                    solicitudList.appendChild(listItem);
                });
            } else {
                const listItem = document.createElement('li');
                listItem.classList.add('solicitud-item');
                listItem.textContent = 'No se encontraron solicitudes.';
                solicitudList.appendChild(listItem);
            }
        })
        .catch(error => {
            console.error('Error al cargar las solicitudes:', error);
            solicitudList.innerHTML = '<li class="solicitud-item error">Error al cargar las solicitudes.</li>';
        });
}
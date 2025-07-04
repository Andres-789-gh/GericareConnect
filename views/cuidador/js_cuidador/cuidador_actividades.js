document.addEventListener('DOMContentLoaded', function() {
    cargarSolicitudes();

    const buscarForm = document.querySelector('.search-container form');
    const buscarSolicitudInput = document.getElementById('buscar-solicitud');
    const solicitudList = document.getElementById('solicitud-list');

    if (buscarForm) {
        buscarForm.addEventListener('submit', function(event) {
            event.preventDefault();
            cargarSolicitudes();
        });
    }

    if (buscarSolicitudInput) {
        buscarSolicitudInput.addEventListener('input', cargarSolicitudes);
    }

    solicitudList.addEventListener('click', function(event) {
        const listItem = event.target.closest('.solicitud-item');
        if (listItem) {
            const solicitudId = listItem.dataset.solicitudId;
            const solicitudTipo = listItem.dataset.solicitudTipo;

            if (solicitudTipo === 'Ingreso') {
                window.location.href = `agregar_paciente.php?solicitud_id=${solicitudId}`;
            } else {
                alert(`Solicitud de tipo: ${solicitudTipo}\nID: ${solicitudId}\n\n${listItem.dataset.solicitudDescripcion}`);
                // Aquí podrías implementar una forma más visual de mostrar los detalles,
                // como un modal o expandiendo el elemento de la lista.
            }
        }
    });
});

function cargarSolicitudes() {
    const buscarTexto = document.getElementById('buscar-solicitud')?.value || '';
    const solicitudList = document.getElementById('solicitud-list');
    solicitudList.innerHTML = '<li class="solicitud-item cargando">Cargando solicitudes...</li>';

    let url = 'admin_solicitudes_obtener.php';
    if (buscarTexto) {
        url += `?buscar-solicitud=${encodeURIComponent(buscarTexto)}`;
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
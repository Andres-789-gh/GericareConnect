document.addEventListener('DOMContentLoaded', function() {
    const buscarForm = document.getElementById('buscarPacientesForm');
    const buscarInput = document.getElementById('buscar-paciente');
    const clearButton = document.getElementById('clear-search-button');
    const pacienteList = document.getElementById('paciente-list');


    cargarPacientes();


    if (buscarForm) {
        buscarForm.addEventListener('submit', (event) => {
            event.preventDefault();
            cargarPacientes(buscarInput.value);
        });
    }
     if (buscarInput) {
         let timeoutId;
         buscarInput.addEventListener('input', () => {
             clearTimeout(timeoutId);
             timeoutId = setTimeout(() => {
                 cargarPacientes(buscarInput.value);
             }, 500);
         });
     }


    if (clearButton) {
        clearButton.addEventListener('click', () => {
            buscarInput.value = '';
            cargarPacientes();
        });
    }


    if (pacienteList) {
        pacienteList.addEventListener('click', function(event) {
            const listItem = event.target.closest('.paciente-item[data-paciente-id]');
            if (listItem && !listItem.classList.contains('cargando') && !listItem.classList.contains('error')) {
                const pacienteId = listItem.dataset.pacienteId;
                const pacienteNombre = listItem.dataset.pacienteNombre;
                const tipoDoc = listItem.dataset.tipoDoc;
                const numDoc = listItem.dataset.numDoc;
                const fechaIngreso = listItem.dataset.fechaIngreso;


                mostrarEstadoActivo(listItem);


                let detalleHtml = `
                    <div style="text-align: left; line-height: 1.8;">
                        <p><strong><i class="fas fa-id-card"></i> Documento:</strong> ${tipoDoc} ${numDoc}</p>
                        <p><strong><i class="fas fa-calendar-alt"></i> Fecha y Hora de Ingreso:</strong> ${fechaIngreso}</p>

                    </div>
                `;


                Swal.fire({
                    title: `<i class="fas fa-user-injured"></i> ${pacienteNombre}`,
                    html: detalleHtml,
                    icon: 'info',
                    confirmButtonText: '<i class="fas fa-times"></i> Cerrar',
                    width: '500px',
                    customClass: {
                        title: 'swal-title-custom',
                        htmlContainer: 'swal-html-custom'
                    }
                });
            }
        });
    }

});

function cargarPacientes(busqueda = '') {
    const pacienteList = document.getElementById('paciente-list');
    pacienteList.innerHTML = '<li class="paciente-item cargando"><i class="fas fa-spinner fa-spin"></i> Cargando familiares...</li>';

    let url = `obtener_pacientes_familiares.php`;
    if (busqueda) {
        url += `?buscar=${encodeURIComponent(busqueda)}`;
    }

    fetch(url)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            pacienteList.innerHTML = '';
            if (data.error) {
                 throw new Error(data.error);
            }

            if (data.pacientes && data.pacientes.length > 0) {
                data.pacientes.forEach(paciente => {
                    const listItem = document.createElement('li');
                    listItem.classList.add('paciente-item', 'animated', 'fadeInUp');

                    listItem.dataset.pacienteId = paciente.id;
                    listItem.dataset.pacienteNombre = `${paciente.nombres} ${paciente.apellidos}`;
                    listItem.dataset.tipoDoc = paciente.tipo_documento;
                    listItem.dataset.numDoc = paciente.documento;
                    listItem.dataset.fechaIngreso = paciente.fecha_ingreso_formateada;


                    listItem.innerHTML = `
                        <div class="paciente-info">
                            <i class="fas fa-user-injured"></i>
                            ${paciente.nombres} ${paciente.apellidos} - ${paciente.tipo_documento}: ${paciente.documento}
                        </div>
                        <span class="activo-status" id="status-${paciente.id}">Activo</span>
                    `;
                    pacienteList.appendChild(listItem);
                });
            } else {
                pacienteList.innerHTML = '<li class="paciente-item no-data"><i class="fas fa-info-circle"></i> No se encontraron pacientes asociados.</li>';
            }
        })
        .catch(error => {
            console.error('Error al cargar los pacientes:', error);
            pacienteList.innerHTML = `<li class="paciente-item error"><i class="fas fa-exclamation-triangle"></i> Error al cargar los familiares: ${error.message}</li>`;
        });
}

function mostrarEstadoActivo(listItem) {
    const pacienteId = listItem.dataset.pacienteId;
    const statusSpan = listItem.querySelector(`#status-${pacienteId}`);

    if (statusSpan) {

        statusSpan.classList.add('visible');


        setTimeout(() => {
             statusSpan.classList.remove('visible');
        }, 3000);
    }
}
document.addEventListener('DOMContentLoaded', function() {
    // Referencias a los elementos del DOM
    const searchForm = document.getElementById('searchForm');
    const busquedaInput = document.getElementById('busquedaInput');
    const pacientesLista = document.getElementById('pacientes-lista');
    const clearButton = document.getElementById('clearButton');

    const cargarPacientesAsignados = (busqueda = '') => {
        pacientesLista.innerHTML = `<li class="paciente-item cargando"><i class="fas fa-spinner fa-spin"></i> Cargando pacientes...</li>`;

        // Llama al nuevo controlador del cuidador
        fetch(`../../../controllers/cuidador/consulta_pacientes_cuidador.php?busqueda=${busqueda}`)
            .then(response => {
                if (!response.ok) {
                    // Intenta leer el mensaje de error del cuerpo de la respuesta
                    return response.json().then(err => { throw new Error(err.error || 'Error de red o del servidor.') });
                }
                return response.json();
            })
            .then(data => {
                pacientesLista.innerHTML = ''; // Limpiar la lista

                if (data.error) { // Manejar errores devueltos por el PHP
                    throw new Error(data.error);
                }

                if (data.length > 0) {
                    data.forEach(paciente => {
                        const li = document.createElement('li');
                        li.className = 'paciente-item';
                        // Muestra la información del paciente
                        li.innerHTML = `
                            <div class="paciente-info">
                                <i class="fas fa-user-nurse"></i>
                                <strong>${paciente.nombre} ${paciente.apellido}</strong>
                                <span>(CC: ${paciente.documento_identificacion})</span>
                            </div>
                            <span class="motivo-asignacion">${paciente.motivo_asignacion || ''}</span>
                        `;
                        pacientesLista.appendChild(li);
                    });
                } else {
                    pacientesLista.innerHTML = `<li class="paciente-item" style="justify-content: center;">No tienes pacientes asignados que coincidan.</li>`;
                }
            })
            .catch(error => {
                console.error('Error al cargar pacientes asignados:', error);
                pacientesLista.innerHTML = `<li class="paciente-item error" style="justify-content: center;">${error.message}</li>`;
            });
    };

    // Carga inicial de los pacientes asignados
    cargarPacientesAsignados();

    // Lógica para el buscador y el botón de limpiar
    let searchTimeout;
    busquedaInput.addEventListener('input', () => {
        clearButton.style.display = busquedaInput.value ? 'block' : 'none';
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            cargarPacientesAsignados(busquedaInput.value);
        }, 400);
    });

    clearButton.addEventListener('click', () => {
        busquedaInput.value = '';
        clearButton.style.display = 'none';
        cargarPacientesAsignados();
        busquedaInput.focus();
    });

    searchForm.addEventListener('submit', e => e.preventDefault());
});

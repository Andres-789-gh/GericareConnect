document.addEventListener('DOMContentLoaded', function() {
    // Referencias a los elementos del DOM
    const searchForm = document.getElementById('searchForm');
    const busquedaInput = document.getElementById('busquedaInput');
    const pacientesLista = document.getElementById('pacientes-lista');

    const cargarPacientes = (busqueda = '') => {
        // Muestra un indicador de carga
        pacientesLista.innerHTML = `<li class="paciente-item cargando"><i class="fas fa-spinner fa-spin"></i> Cargando...</li>`;

        // Llama al nuevo controlador para obtener los datos
        fetch(`../../../controllers/familiar/consulta_pacientes_familiar.php?busqueda=${busqueda}`)
            .then(response => {
                if (!response.ok) throw new Error('Error de red o del servidor.');
                return response.json();
            })
            .then(data => {
                pacientesLista.innerHTML = ''; // Limpiar la lista

                if (data.error) throw new Error(data.error);

                if (data.length > 0) {
                    data.forEach(paciente => {
                        const li = document.createElement('li');
                        li.className = 'paciente-item';
                        li.innerHTML = `
                            <div class="paciente-info">
                                <i class="fas fa-user-injured"></i>
                                <strong>${paciente.nombre} ${paciente.apellido}</strong>
                                <span>(CC: ${paciente.documento_identificacion})</span>
                            </div>
                            <span class="estado">${paciente.estado}</span>
                        `;
                        pacientesLista.appendChild(li);
                    });
                } else {
                    pacientesLista.innerHTML = `<li class="paciente-item" style="justify-content: center;">No se encontraron pacientes.</li>`;
                }
            })
            .catch(error => {
                console.error('Error al cargar pacientes:', error);
                pacientesLista.innerHTML = `<li class="paciente-item error" style="justify-content: center;">${error.message}</li>`;
            });
    };

    // Carga inicial de todos los pacientes del familiar
    cargarPacientes();

    let searchTimeout;
    busquedaInput.addEventListener('input', () => {
        // Muestra o oculta el botón "x" si hay texto o no
        clearButton.style.display = busquedaInput.value ? 'block' : 'none';

        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            cargarPacientes(busquedaInput.value);
        }, 400);
    });

    // limpiar la búsqueda al hacer clic en la "x"
    clearButton.addEventListener('click', () => {
        busquedaInput.value = '';
        clearButton.style.display = 'none';
        cargarPacientes(); 
        busquedaInput.focus();
    });

    searchForm.addEventListener('submit', e => e.preventDefault());
});
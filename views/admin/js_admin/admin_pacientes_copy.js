document.addEventListener('DOMContentLoaded', function() {
    // Referencias a los elementos del nuevo buscador en la vista
    const searchForm = document.getElementById('universalSearchForm');
    const filtroRol = document.getElementById('filtro_rol');
    const terminoBusqueda = document.getElementById('termino_busqueda');
    const resultsContainer = document.getElementById('resultsContainer');
    
    const idAdmin = document.body.dataset.idAdmin || 0;

    /* Función principal para realizar la búsqueda */
    const performSearch = () => {
        const rol = filtroRol.value;
        const busqueda = terminoBusqueda.value;

        // Mensaje de "cargando" mientras se obtienen los datos
        resultsContainer.innerHTML = `<li class="result-item" style="justify-content: center;"><i class="fas fa-spinner fa-spin"></i> Buscando...</li>`;

        // Llama al controlador PHP para obtener los resultados
        fetch(`../../../controllers/admin/consulta_controller.php?filtro=${rol}&busqueda=${busqueda}&id_admin=${idAdmin}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error de red al intentar contactar al servidor.');
                }
                return response.json();
            })
            .then(data => {
                resultsContainer.innerHTML = ''; // Limpiar resultados anteriores

                if (data.error) {
                    throw new Error(data.error);
                }

                if (data.length > 0) {
                    // Si hay resultados los muestra uno por uno
                    data.forEach(item => {
                        const li = document.createElement('li');
                        li.className = `result-item tipo-${item.rol.toLowerCase().replace(' ', '-')}`;
                        
                        li.innerHTML = `
                            <div class="info">
                                <strong>${item.nombre_completo}</strong>
                                <span>(CC: ${item.documento})</span>
                                <span class="rol rol-${item.rol.toLowerCase().replace(' ', '-')}">${item.rol}</span>
                            </div>
                            <div class="contacto">
                                <i class="fas fa-envelope"></i> ${item.contacto || 'N/A'}
                            </div>
                        `;
                        resultsContainer.appendChild(li);
                    });
                } else {
                    // Si no hay resultados muestra un mensaje de que no se encontraron los resultados
                    resultsContainer.innerHTML = `<li class="result-item" style="justify-content: center; color: #777;">No se encontraron resultados.</li>`;
                }
            })
            .catch(error => {
                console.error('Error en la búsqueda:', error);
                resultsContainer.innerHTML = `<li class="result-item" style="justify-content: center; color: red; font-weight: bold;">Error al buscar: ${error.message}</li>`;
            });
    };

    // asignacion de eventos
    
    // Buscar cuando el usuario cambia el filtro de rol
    if(filtroRol) {
        filtroRol.addEventListener('change', performSearch);
    }

    // Buscar mientras el usuario escribe
    let searchTimeout;
    if(terminoBusqueda) {
        terminoBusqueda.addEventListener('input', () => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(performSearch, 400); 
        });
    }

    // Evitar que el formulario se envíe de forma tradicional
    if(searchForm) {
        searchForm.addEventListener('submit', (e) => e.preventDefault());
    }
});

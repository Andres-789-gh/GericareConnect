document.addEventListener('DOMContentLoaded', function() {
    
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            alert.classList.add('fade-out');
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });

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
                            <div class="actions">
                                <i class="fas fa-trash-alt action-icon delete-icon" 
                                   data-id="${item.id}" 
                                   data-tipo="${item.tipo_entidad}" 
                                   data-nombre="${item.nombre_completo}"
                                   title="Desactivar"></i>
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

    // Event listener para la acción de eliminar 
    resultsContainer.addEventListener('click', function(event) {
        if (event.target.classList.contains('delete-icon')) {
            const id = event.target.dataset.id;
            const tipo = event.target.dataset.tipo;
            const nombre = event.target.dataset.nombre;

            Swal.fire({
                title: `¿Estás seguro?`,
                text: `Se desactivará a ${nombre}. Esta acción se puede revertir, pero el usuario no podrá iniciar sesión.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, desactivar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    desactivarEntidad(id, tipo);
                }
            });
        }
    });

    const desactivarEntidad = (id, tipo) => {
        const formData = new FormData();
        formData.append('id', id);
        formData.append('tipo', tipo);

        fetch('../../../controllers/admin/desactivar_controller.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire('¡Desactivado!', data.message, 'success');
                // Refrescar la búsqueda para que el usuario desaparezca de la lista
                performSearch(); 
            } else {
                Swal.fire('Error', data.message, 'error');
            }
        })
        .catch(error => {
            Swal.fire('Error de Conexión', 'No se pudo completar la solicitud.', 'error');
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

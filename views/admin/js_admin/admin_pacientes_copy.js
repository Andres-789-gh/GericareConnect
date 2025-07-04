document.addEventListener('DOMContentLoaded', function() {
    
    // Oculta las notificaciones de la sesión después de 5 segundos
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            alert.classList.add('fade-out');
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });

    // Referencias a los elementos del buscador
    const searchForm = document.getElementById('universalSearchForm');
    const filtroRol = document.getElementById('filtro_rol');
    const terminoBusqueda = document.getElementById('termino_busqueda');
    const resultsContainer = document.getElementById('resultsContainer');
    
    // Obtiene el ID del admin actual para no mostrarlo en la lista
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
                    data.forEach(item => {
                        const li = document.createElement('li');
                        li.className = `result-item tipo-${item.rol.toLowerCase().replace(' ', '-')}`;
                        
                        // Enlace para editar al hacer clic en el nombre (aún por implementar en el destino)
                        const editLink = `<a href="form_paciente.php?id=${item.id}" class="edit-link">${item.nombre_completo}</a>`;

                        li.innerHTML = `
                            <div class="info">
                                <strong>${item.rol === 'Paciente' ? editLink : item.nombre_completo}</strong>
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
                    resultsContainer.innerHTML = `<li class="result-item" style="justify-content: center; color: #777;">No se encontraron resultados.</li>`;
                }
            })
            .catch(error => {
                console.error('Error en la búsqueda:', error);
                resultsContainer.innerHTML = `<li class="result-item" style="justify-content: center; color: red; font-weight: bold;">Error al buscar: ${error.message}</li>`;
            });
    };
    
    // ========== ¡CAMBIO CLAVE AQUÍ! ==========
    // Ejecuta la búsqueda una vez tan pronto como la página carga.
    performSearch();
    // =======================================

    // Evento para desactivar usuarios/pacientes
    resultsContainer.addEventListener('click', function(event) {
        if (event.target.classList.contains('delete-icon')) {
            const id = event.target.dataset.id;
            const tipo = event.target.dataset.tipo;
            const nombre = event.target.dataset.nombre;

            Swal.fire({
                title: `¿Estás seguro?`,
                text: `Se eliminara a ${nombre}. El usuario no podrá iniciar sesión.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
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
                Swal.fire('¡Eliminado!', data.message, 'success');
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

    // Eventos para el buscador
    if(filtroRol) {
        filtroRol.addEventListener('change', performSearch);
    }
    if(terminoBusqueda) {
        let searchTimeout;
        terminoBusqueda.addEventListener('input', () => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(performSearch, 400); 
        });
    }
    if(searchForm) {
        searchForm.addEventListener('submit', (e) => e.preventDefault());
    }
});
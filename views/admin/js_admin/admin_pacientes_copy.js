document.addEventListener('DOMContentLoaded', function() {
    const filtroRol = document.getElementById('filtro_rol');
    const terminoBusqueda = document.getElementById('termino_busqueda');
    const resultsContainer = document.getElementById('resultsContainer');
    const idAdmin = document.body.dataset.idAdmin || 0;

    // Función principal que busca y PINTA los resultados con el diseño PRO
    const performSearch = () => {
        const rol = filtroRol.value;
        const busqueda = terminoBusqueda.value;
        resultsContainer.innerHTML = `<li class="result-item" style="justify-content: center;"><i class="fas fa-spinner fa-spin"></i> Buscando...</li>`;

        fetch(`../../../controllers/admin/consulta_controller.php?filtro=${rol}&busqueda=${busqueda}&id_admin=${idAdmin}`)
        .then(response => response.json())
        .then(data => {
            resultsContainer.innerHTML = '';
            if (data.error) throw new Error(data.error);

            if (data.length > 0) {
                // Se crea una tabla con estilos dentro del contenedor de resultados
                let contentHTML = `
                    <table class="results-table">
                        <thead>
                            <tr>
                                <th>Nombre Completo</th>
                                <th>Documento</th>
                                <th>Rol</th>
                                <th>Edad</th>
                                <th>Género</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>`;
                
                data.forEach(item => {
                    let edad = 'N/A';
                    if (item.fecha_nacimiento) {
                        const birthDate = new Date(item.fecha_nacimiento);
                        const today = new Date();
                        edad = today.getFullYear() - birthDate.getFullYear();
                        const m = today.getMonth() - birthDate.getMonth();
                        if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
                            edad--;
                        }
                    }

                    let generoHtml = 'N/A';
                    if (item.rol === 'Paciente' && item.genero) {
                        generoHtml = item.genero === 'Masculino' 
                            ? `<span class="genero-masculino"><i class="fas fa-mars"></i> Masculino</span>` 
                            : `<span class="genero-femenino"><i class="fas fa-venus"></i> Femenino</span>`;
                    }
                    
                    let rolClass = item.rol ? item.rol.toLowerCase() : '';

                    contentHTML += `
                        <tr>
                            <td>${item.nombre_completo}</td>
                            <td>${item.documento}</td>
                            <td><span class="rol-tag rol-${rolClass}">${item.rol}</span></td>
                            <td>${edad}</td>
                            <td>${generoHtml}</td>
                            <td class="actions">
                                ${item.tipo_entidad === 'Paciente' ? `<a href="agregar_paciente.php?id=${item.id}" title="Editar"><i class="fas fa-edit"></i></a>` : ''}
                                <button class="delete-button" data-id="${item.id}" data-tipo="${item.tipo_entidad}" data-nombre="${item.nombre_completo}" title="Desactivar"><i class="fas fa-trash-alt"></i></button>
                            </td>
                        </tr>`;
                });

                contentHTML += `</tbody></table>`;
                resultsContainer.innerHTML = contentHTML;

            } else {
                resultsContainer.innerHTML = `<li class="result-item" style="justify-content: center; color: #777;">No se encontraron resultados.</li>`;
            }
        })
        .catch(error => {
            resultsContainer.innerHTML = `<li class="result-item" style="color:red; font-weight:bold; justify-content:center;">Error al buscar: ${error.message}</li>`;
        });
    };
    
    // Carga inicial de datos al entrar a la página
    performSearch();

    // Eventos para que la búsqueda sea dinámica
    filtroRol.addEventListener('change', performSearch);
    let searchTimeout;
    terminoBusqueda.addEventListener('input', () => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(performSearch, 400);
    });

    // Delegación de eventos para el botón de borrar
    resultsContainer.addEventListener('click', function(event) {
        const deleteButton = event.target.closest('.delete-button');
        if (deleteButton) {
            const id = deleteButton.dataset.id;
            const tipo = deleteButton.dataset.tipo;
            const nombre = deleteButton.dataset.nombre;
            confirmarDesactivacion(id, tipo, nombre);
        }
    });
});

// FUNCIÓN DE BORRADO QUE AHORA SÍ FUNCIONA
function confirmarDesactivacion(id, tipo, nombre) {
    Swal.fire({
        title: `¿Estás seguro de desactivar a ${nombre}?`,
        text: "El usuario no podrá iniciar sesión o aparecerá como inactivo.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, ¡desactivar!',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
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
                    // Recargamos la lista para que se vea el cambio al instante
                    document.getElementById('filtro_rol').dispatchEvent(new Event('change'));
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            })
            .catch(error => Swal.fire('Error de Red', 'No se pudo completar la solicitud.', 'error'));
        }
    });
}
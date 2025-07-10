    document.addEventListener('DOMContentLoaded', function() {
        const filtroEstado = document.getElementById('filtro_estado');
        const terminoBusqueda = document.getElementById('termino_busqueda');
        const tablaResultados = document.getElementById('tablaResultadosAdminActividad');

        const performSearch = () => {
            const estado = filtroEstado.value;
            const busqueda = terminoBusqueda.value;
            
            tablaResultados.innerHTML = '<tr><td colspan="5" style="text-align:center;"><i class="fas fa-spinner fa-spin"></i> Buscando...</td></tr>';

            // URL del nuevo controlador que devuelve JSON
            const url = `../../../controllers/admin/actividad/buscar_actividades_controller.php?estado=${encodeURIComponent(estado)}&busqueda=${encodeURIComponent(busqueda)}`;

            fetch(url)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error en la red o respuesta no válida del servidor.');
                    }
                    return response.json();
                })
                .then(data => {
                    tablaResultados.innerHTML = '';
                    
                    if (data.error) {
                        throw new Error(data.error);
                    }

                    if (data.length === 0) {
                        tablaResultados.innerHTML = '<tr><td colspan="5" style="text-align:center;">No se encontraron resultados.</td></tr>';
                        return;
                    }

                    data.forEach(actividad => {
                        const fecha = new Date(actividad.fecha_actividad + 'T00:00:00').toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' });
                        
                        let accionesHtml = '';
                        if (actividad.estado_actividad === 'Pendiente') {
                            // Añadimos las clases 'action-icon' y 'edit-icon'
                            accionesHtml += `<a href="form_actividades.php?id=${actividad.id_actividad}" class="action-icon edit-icon" title="Editar"><i class="fas fa-pencil-alt"></i></a>`;
                        }
                        // El botón también usa la clase 'action-icon'
                        accionesHtml += `<button class="action-icon" onclick="confirmarDesactivacion(${actividad.id_actividad})" title="Eliminar Actividad"><i class="fas fa-trash-alt"></i></button>`;
                        
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${escapeHTML(actividad.tipo_actividad)}</td>
                            <td>${escapeHTML(actividad.nombre_paciente)}</td>
                            <td>${fecha}</td>
                            <td>${escapeHTML(actividad.estado_actividad)}</td>
                            <td class="actions">${accionesHtml}</td>
                        `;
                        tablaResultados.appendChild(row);
                    });
                })
                .catch(error => {
                    console.error('Error en fetch:', error);
                    tablaResultados.innerHTML = `<tr><td colspan="5" style="color:red; font-weight:bold; text-align:center;">Error al realizar la búsqueda: ${error.message}</td></tr>`;
                });
        };

        function escapeHTML(str) {
            if (str === null || str === undefined) return '';
            const p = document.createElement('p');
            p.appendChild(document.createTextNode(str));
            return p.innerHTML;
        }

        filtroEstado.addEventListener('change', performSearch);

        let searchTimeout;
        terminoBusqueda.addEventListener('input', () => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(performSearch, 400);
        });
    });
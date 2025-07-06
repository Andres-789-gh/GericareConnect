document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const tableBody = document.getElementById('historias-clinicas-tbody');

    // Función para renderizar las filas de la tabla
    const renderTableRows = (data) => {
        tableBody.innerHTML = ''; // Limpiar la tabla actual

        if (data.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="6">No se encontraron resultados.</td></tr>';
            return;
        }

        data.forEach(historia => {
            // Limitar el motivo de la consulta para que no sea muy largo
            const motivoCorto = historia.motivo_consulta.substring(0, 50) + '...';

            const row = `
                <tr>
                    <td>${historia.id_historia_clinica}</td>
                    <td>${historia.nombre} ${historia.apellido}</td>
                    <td>${historia.fecha_creacion}</td>
                    <td>${historia.fecha_actualizacion}</td>
                    <td>${motivoCorto}</td>
                    <td class="actions">
                        <a href="form_historia_clinica.php?id=${historia.id_historia_clinica}" class="btn-edit">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                    </td>
                </tr>
            `;
            tableBody.innerHTML += row;
        });
    };

    // Evento para buscar al escribir en el input
    searchInput.addEventListener('keyup', async (e) => {
        const searchTerm = e.target.value;

        // Evita hacer peticiones vacías, mejora el rendimiento
        if (searchTerm.length < 1) {
            // Podrías recargar la lista completa o simplemente no hacer nada hasta que haya al menos 3 caracteres
             searchTerm = ""; // si está vacío, que busque todo
        }

        try {
            // La URL del controlador que maneja la búsqueda
            const response = await fetch(`../../../../controllers/admin/HC/historia_clinica_controlador.php?search=${encodeURIComponent(searchTerm)}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest' // Importante para que el controlador sepa que es una petición AJAX
                }
            });

            if (!response.ok) {
                throw new Error('La respuesta de la red no fue correcta.');
            }

            const resultados = await response.json();
            renderTableRows(resultados);

        } catch (error) {
            console.error('Error al realizar la búsqueda:', error);
            tableBody.innerHTML = '<tr><td colspan="6">Error al cargar los datos. Intente de nuevo.</td></tr>';
        }
    });
});

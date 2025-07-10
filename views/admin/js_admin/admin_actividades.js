document.addEventListener('DOMContentLoaded', function() {
    const tabla = document.querySelector('.activities-table tbody');

    if (tabla) {
        tabla.addEventListener('click', function(event) {
            // Encuentra el botón más cercano en el que se hizo clic
            const boton = event.target.closest('.btn-action');

            if (!boton) {
                return; // No se hizo clic en un botón de acción
            }

            // Encuentra la fila (tr) a la que pertenece el botón
            const fila = boton.closest('tr');
            const idActividad = fila.dataset.id;

            // Lógica para el botón de EDITAR
            if (boton.classList.contains('btn-edit')) {
                // Redirige al formulario de edición con el ID de la actividad
                window.location.href = `form_actividades.php?id=${idActividad}`;
            }

            // Lógica para el botón de ELIMINAR
            if (boton.classList.contains('btn-delete')) {
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "¡No podrás revertir esta acción!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, ¡bórrala!',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Si el usuario confirma, envía la solicitud al servidor
                        fetch('../../../controllers/admin/actividad/actividad_controller.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: `accion=eliminar&id_actividad=${idActividad}`
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire(
                                    '¡Eliminada!',
                                    'La actividad ha sido eliminada.',
                                    'success'
                                );
                                // Elimina la fila de la tabla visualmente
                                fila.remove();
                            } else {
                                Swal.fire(
                                    'Error',
                                    data.message || 'No se pudo eliminar la actividad.',
                                    'error'
                                );
                            }
                        })
                        .catch(error => {
                            Swal.fire('Error', 'Hubo un problema de conexión.', 'error');
                        });
                    }
                });
            }
        });
    }
});
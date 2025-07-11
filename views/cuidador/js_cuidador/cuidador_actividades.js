document.addEventListener('DOMContentLoaded', () => {
    const listaActividades = document.getElementById('actividades-lista');
    const filtroEstado = document.getElementById('filtro_estado');
    const filtroPaciente = document.getElementById('filtro_paciente');
    const idCuidador = 1; // TO-DO: Idealmente, obtén esto de la sesión o de un atributo en el DOM.

    const cargarActividades = async () => {
        const estado = filtroEstado.value;
        const paciente = filtroPaciente.value;
        listaActividades.innerHTML = '<div class="actividad-card-cargando"><i class="fas fa-spinner fa-spin"></i> Cargando actividades...</div>';

        try {
            // URL del controlador que devuelve las actividades.
            const url = `../../../controllers/cuidador/actividades_cuidador.php?id_cuidador=${idCuidador}&estado=${estado}&paciente=${encodeURIComponent(paciente)}`;
            const respuesta = await fetch(url);
            if (!respuesta.ok) throw new Error('Error en la respuesta del servidor');
            
            const actividades = await respuesta.json();
            listaActividades.innerHTML = '';

            if (actividades.length === 0) {
                listaActividades.innerHTML = '<div class="actividad-card-cargando">No hay actividades para mostrar con los filtros seleccionados.</div>';
                return;
            }

            actividades.forEach(act => {
                const claseEstado = act.estado_actividad.toLowerCase();
                const icono = claseEstado === 'completada' ? 'fa-check-circle' : 'fa-clock';
                
                // Formateo de fecha
                const fecha = new Date(act.fecha_actividad + 'T00:00:00'); // Asegura que se interprete como local
                const fechaFormateada = fecha.toLocaleDateString('es-ES', { year: 'numeric', month: 'long', day: 'numeric' });

                const actividadHTML = `
                    <div class="actividad-card ${claseEstado} animated fadeInUp">
                        <div class="actividad-estado-icono">
                            <i class="fas ${icono}"></i>
                        </div>
                        <div class="actividad-info">
                            <strong>${act.tipo_actividad}</strong>
                            <span>Paciente: ${act.nombre_paciente} | Fecha: ${fechaFormateada}</span>
                        </div>
                        <div class="actividad-acciones">
                            ${claseEstado === 'pendiente' ? `<button class="btn-accion completar" onclick="completarActividad(${act.id_actividad})">Completar</button>` : ''}
                        </div>
                    </div>
                `;
                listaActividades.insertAdjacentHTML('beforeend', actividadHTML);
            });

        } catch (error) {
            console.error('Error al cargar actividades:', error);
            listaActividades.innerHTML = '<div class="actividad-card-cargando error"><i class="fas fa-exclamation-triangle"></i> Error al cargar datos.</div>';
        }
    };

    // Eventos para los filtros.
    filtroEstado.addEventListener('change', cargarActividades);
    filtroPaciente.addEventListener('input', () => {
        // Un pequeño delay para no hacer fetch en cada tecla.
        setTimeout(cargarActividades, 300);
    });

    // Carga inicial de actividades.
    cargarActividades();
});

// Función para el botón de completar (requiere SweetAlert2).
function completarActividad(id) {
    Swal.fire({
        title: '¿Confirmar actividad?',
        text: "¿Estás seguro de que quieres marcar esta actividad como completada?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, completar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Aquí iría la lógica para enviar el formulario al controlador que completa la actividad.
            // Por ejemplo, crear un form y enviarlo.
            alert(`Funcionalidad para completar la actividad con ID: ${id} se debe implementar aquí.`);
        }
    });
}
document.addEventListener('DOMContentLoaded', function() {
    // Manejar el envío del formulario para asignar un nuevo medicamento
    const formAsignarMedicamento = document.getElementById('form-asignar-medicamento');
    if (formAsignarMedicamento) {
        formAsignarMedicamento.addEventListener('submit', function(e) {
            e.preventDefault();
            asignarMedicamento();
        });
    }
});

const idHc = document.getElementById('secciones-asignacion')?.dataset.idHc;
const ajaxUrl = '../../../controllers/admin/HC/gestion_asignaciones_controller.php';

// Función genérica para enviar datos con fetch
async function enviarPeticion(data) {
    const formData = new FormData();
    for (const key in data) {
        formData.append(key, data[key]);
    }

    try {
        const response = await fetch(ajaxUrl, {
            method: 'POST',
            body: formData
        });
        if (!response.ok) {
            throw new Error(`Error HTTP: ${response.status}`);
        }
        return await response.json();
    } catch (error) {
        console.error('Error en la petición:', error);
        Swal.fire('Error de Red', 'No se pudo comunicar con el servidor.', 'error');
        return { success: false };
    }
}

// --- LÓGICA DE ENFERMEDADES ---

async function asignarEnfermedad() {
    const select = document.getElementById('select-enfermedad');
    const idEnfermedad = select.value;
    if (!idEnfermedad) {
        Swal.fire('Atención', 'Por favor, seleccione una enfermedad.', 'warning');
        return;
    }

    const data = {
        accion: 'asignar_enfermedad',
        id_historia_clinica: idHc,
        id_enfermedad: idEnfermedad
    };

    const resultado = await enviarPeticion(data);

    if (resultado.success) {
        const nombreEnfermedad = select.options[select.selectedIndex].text;
        const nuevoElemento = `
            <div class="item-asignado" id="enf-${resultado.id_asignacion}">
                <span>${nombreEnfermedad}</span>
                <button class="btn-delete" onclick="eliminarAsignacion(${resultado.id_asignacion}, 'enfermedad')"><i class="fas fa-trash"></i></button>
            </div>`;
        
        const lista = document.getElementById('lista-enfermedades-asignadas');
        // Quitar el mensaje de "lista vacía" si existe
        const emptyMsg = lista.querySelector('.empty-message');
        if (emptyMsg) emptyMsg.remove();
        
        lista.insertAdjacentHTML('beforeend', nuevoElemento);
        select.value = ""; // Resetear el select
    } else {
        Swal.fire('Error', resultado.message || 'No se pudo asignar la enfermedad.', 'error');
    }
}

// --- LÓGICA DE MEDICAMENTOS ---

async function asignarMedicamento() {
    const idMedicamento = document.getElementById('select-medicamento').value;
    const dosis = document.getElementById('input-dosis').value;
    const frecuencia = document.getElementById('input-frecuencia').value;
    const instrucciones = document.getElementById('input-instrucciones').value;

    if (!idMedicamento || !dosis || !frecuencia) {
        Swal.fire('Campos requeridos', 'Debe seleccionar un medicamento e indicar la dosis y frecuencia.', 'warning');
        return;
    }

    const data = {
        accion: 'asignar_medicamento',
        id_historia_clinica: idHc,
        id_medicamento: idMedicamento,
        dosis,
        frecuencia,
        instrucciones
    };

    const resultado = await enviarPeticion(data);

    if (resultado.success) {
        location.reload(); // La forma más simple de actualizar la lista con los nuevos datos
    } else {
        Swal.fire('Error', resultado.message || 'No se pudo recetar el medicamento.', 'error');
    }
}

async function editarMedicamento(medicamento) {
    const { value: formValues } = await Swal.fire({
        title: `Editando ${medicamento.nombre_medicamento}`,
        html: `
            <input id="swal-dosis" class="swal2-input" value="${medicamento.dosis}" placeholder="Dosis">
            <input id="swal-frecuencia" class="swal2-input" value="${medicamento.frecuencia}" placeholder="Frecuencia">
            <textarea id="swal-instrucciones" class="swal2-textarea" placeholder="Instrucciones">${medicamento.instrucciones}</textarea>
        `,
        focusConfirm: false,
        preConfirm: () => {
            return {
                dosis: document.getElementById('swal-dosis').value,
                frecuencia: document.getElementById('swal-frecuencia').value,
                instrucciones: document.getElementById('swal-instrucciones').value
            };
        }
    });

    if (formValues) {
        const data = {
            accion: 'actualizar_medicamento',
            id_hc_medicamento: medicamento.id_hc_medicamento,
            ...formValues
        };
        const resultado = await enviarPeticion(data);
        if(resultado.success) location.reload();
    }
}


// --- LÓGICA COMÚN DE ELIMINACIÓN ---

function eliminarAsignacion(idAsignacion, tipo) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: `Se eliminará esta asignación de la historia clínica.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonText: 'Cancelar',
        confirmButtonText: 'Sí, eliminar'
    }).then(async (result) => {
        if (result.isConfirmed) {
            const data = {
                accion: `eliminar_${tipo}`,
                [`id_hc_${tipo}`]: idAsignacion
            };
            
            const resultado = await enviarPeticion(data);

            if (resultado.success) {
                const prefijo = tipo === 'enfermedad' ? 'enf' : 'med';
                document.getElementById(`${prefijo}-${idAsignacion}`).remove();
                 Swal.fire('Eliminado', 'La asignación ha sido eliminada.', 'success');
            } else {
                Swal.fire('Error', resultado.message || 'No se pudo eliminar la asignación.', 'error');
            }
        }
    });
}
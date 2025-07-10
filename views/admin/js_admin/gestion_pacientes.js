// Espera a que todo el contenido del HTML se haya cargado
document.addEventListener('DOMContentLoaded', function() {
    
    const form = document.getElementById('pacienteForm');

    if (form) {
        form.addEventListener('submit', function(event) {
            event.preventDefault(); // Previene el envío tradicional

            const formData = new FormData(form);

            // Validación simple de campos
            let camposValidos = true;
            for (let [key, value] of formData.entries()) {
                // Asumimos que los campos requeridos no deben estar vacíos
                if (key !== 'id_paciente' && !value) {
                    camposValidos = false;
                    break;
                }
            }

            if (!camposValidos) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Por favor, completa todos los campos requeridos.',
                });
                return;
            }

            // Envía los datos usando Fetch API
            fetch(form.action, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        // Redirige a la lista de pacientes
                        window.location.href = 'admin_pacientes.php';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'Ocurrió un error al procesar la solicitud.',
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error de Conexión',
                    text: 'No se pudo comunicar con el servidor.',
                });
            });
        });
    }
});
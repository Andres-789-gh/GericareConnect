document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('form-paciente');

    if (!form) {
        return; // Exit if the form is not on the page
    }

    form.addEventListener('submit', function(e) {
        e.preventDefault(); // Stop the default browser submission

        const formData = new FormData(form);
        const submitButton = form.querySelector('button[type="submit"]');
        const originalButtonHtml = submitButton.innerHTML;

        // If in edit mode, the 'tipo_sangre' select is disabled, so its value isn't included in formData.
        // We need to add it manually to the data being sent.
        const tipoSangreSelect = document.getElementById('tipo_sangre');
        if (tipoSangreSelect && tipoSangreSelect.disabled) {
            formData.append('tipo_sangre', tipoSangreSelect.value);
        }

        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';

        fetch('../../../controllers/admin/paciente_controller.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Operación Exitosa!',
                    text: data.message,
                    timer: 2000,
                    showConfirmButton: false,
                    allowOutsideClick: false
                }).then(() => {
                    window.location.href = 'admin_pacientes.php';
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops... Hubo un Error',
                    text: data.message || 'No se pudo completar la operación.'
                });
            }
        })
        .catch(error => {
            console.error('Fetch Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error de Conexión',
                text: 'No se pudo comunicar con el servidor. Por favor, inténtelo de nuevo.'
            });
        })
        .finally(() => {
            // Re-enable the button and restore its original content
            submitButton.disabled = false;
            submitButton.innerHTML = originalButtonHtml;
        });
    });
});
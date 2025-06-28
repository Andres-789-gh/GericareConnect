document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("registerForm");
    const inputs = document.querySelectorAll("input, select");
    const errorContainer = document.getElementById('error-container');
    const errorMessageElement = document.getElementById('error-message');
    const urlParams = new URLSearchParams(window.location.search);
    const error = urlParams.get('error');
    const togglePassword = document.getElementById('togglePassword');
    const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
    const passwordField = document.getElementById('password');
    const confirmPasswordField = document.getElementById('confirm_password');

    form.style.opacity = "0";
    form.style.transform = "translateY(-20px)";
    setTimeout(() => {
        form.style.transition = "opacity 1s ease, transform 1s ease";
        form.style.opacity = "1";
        form.style.transform = "translateY(0)";
    }, 500);

    inputs.forEach(input => {
        input.addEventListener("focus", () => {
            input.style.transform = "scale(1.05)";
            input.style.transition = "transform 0.3s ease-in-out";
        });

        input.addEventListener("blur", () => {
            input.style.transform = "scale(1)";
        });
    });

    form.addEventListener("submit", function (e) {
        // Validar contraseñas
        let passwordValue = document.querySelector("input[name='password']").value;
        let confirmPasswordValue = document.querySelector("input[name='confirm_password']").value;

        if (passwordValue !== confirmPasswordValue) {
            e.preventDefault();
            alert("Las contraseñas no coinciden. Inténtalo de nuevo.");
            return;
        }

        // Validar que al menos un rol esté seleccionado
        const rolesSeleccionados = document.querySelectorAll("input[name='roles[]']:checked");
        if (rolesSeleccionados.length === 0) {
            e.preventDefault();
            alert("Debes seleccionar al menos un rol.");
            return;
        }

        // Validar campos específicos por rol
        const rolAdmin = document.getElementById("rol-admin").checked;
        const rolCuidador = document.getElementById("rol-cuidador").checked;
        const rolFamiliar = document.getElementById("rol-familiar").checked;

        if ((rolAdmin || rolCuidador)) {
            const fechaContratacion = document.getElementById("fecha_contratacion").value.trim();
            const tipoContrato = document.getElementById("tipo_contrato").value.trim();
            const contactoEmergencia = document.getElementById("contacto_emergencia").value.trim();
            if (!fechaContratacion || !tipoContrato || !contactoEmergencia) {
                e.preventDefault();
                alert("Debes completar todos los campos laborales si deseas registrarte como administrador o cuidador.");
                return;
            }
        }

        if (rolFamiliar) {
            const parentesco = document.getElementById("parentesco").value.trim();
            if (!parentesco) {
                e.preventDefault();
                alert("Debes completar el campo parentesco si deseas registrarte como familiar.");
                return;
            }
        }
    });


    if (error === 'Este correo ya está registrado.' || error === 'Este documento ya está registrado.') {
        errorMessageElement.textContent = error;
        errorContainer.style.display = 'block';
    } else {
        errorContainer.style.display = 'none';
    }

    if (togglePassword) {
        togglePassword.addEventListener('click', function () {
            const icon = this.querySelector('i');
            passwordField.type = passwordField.type === "password" ? "text" : "password";
            icon.classList.toggle("fa-eye-slash");
        });
    }

    if (toggleConfirmPassword) {
        toggleConfirmPassword.addEventListener('click', function () {
            const icon = this.querySelector('i');
            confirmPasswordField.type = confirmPasswordField.type === "password" ? "text" : "password";
            icon.classList.toggle("fa-eye-slash");
        });
    }
});


document.addEventListener("DOMContentLoaded", function () {
    const rolAdmin = document.getElementById("rol-admin");
    const rolCuidador = document.getElementById("rol-cuidador");
    const rolFamiliar = document.getElementById("rol-familiar");

    const camposCuidadorAdmin = document.getElementById("campos-cuidador-admin");
    const camposFamiliar = document.getElementById("campos-familiar");

    function actualizarVisibilidadCampos() {
        // Mostrar campos correspondientes
        camposCuidadorAdmin.style.display = (rolCuidador.checked || rolAdmin.checked) ? "block" : "none";
        camposFamiliar.style.display = rolFamiliar.checked ? "block" : "none";
    }

    function controlarSeleccion(exclusivo, conflictivo) {
        exclusivo.addEventListener("change", () => {
            if (exclusivo.checked && conflictivo.checked) {
                conflictivo.checked = false;
            }
            actualizarVisibilidadCampos();
        });
    }

    // Reglas específicas
    rolAdmin.addEventListener("change", () => {
        if (rolAdmin.checked && rolCuidador.checked) {
            rolCuidador.checked = false;
        }
        actualizarVisibilidadCampos();
    });

    rolCuidador.addEventListener("change", () => {
        if (rolCuidador.checked && rolAdmin.checked) {
            rolAdmin.checked = false;
        }
        actualizarVisibilidadCampos();
    });

    rolFamiliar.addEventListener("change", () => {
        actualizarVisibilidadCampos(); // familiar no interfiere
    });

    actualizarVisibilidadCampos(); // iniciar al cargar
});

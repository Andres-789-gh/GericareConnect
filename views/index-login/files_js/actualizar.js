document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('registerForm');
    if (!form) {
        return; // Si no encuentra el formulario no se hace nada
    }

    form.addEventListener('submit', function (event) {
        // Prevenir el envío del formulario para poder validarlo primero
        event.preventDefault();

        let isValid = true;

        // Limpiar errores anteriores
        document.querySelectorAll('.mensaje-error-campo').forEach(el => el.textContent = '');

        // Validación de Campos

        // Nombre
        const nombre = document.getElementById('nombre');
        if (nombre.value.trim() === '') {
            document.getElementById('error-nombre').textContent = 'El nombre es obligatorio.';
            isValid = false;
        }

        // Apellido
        const apellido = document.getElementById('apellido');
        if (apellido.value.trim() === '') {
            document.getElementById('error-apellido').textContent = 'El apellido es obligatorio.';
            isValid = false;
        }

        // Fecha de Nacimiento
        const fechaNacimiento = document.getElementById('fecha_nacimiento');
        if (fechaNacimiento.value === '') {
            document.getElementById('error-fecha_nacimiento').textContent = 'La fecha de nacimiento es obligatoria.';
            isValid = false;
        }

        // Dirección
        const direccion = document.getElementById('direccion');
        if (direccion.value.trim() === '') {
            document.getElementById('error-direccion').textContent = 'La dirección es obligatoria.';
            isValid = false;
        }

        // Correo Electrónico
        const correo = document.getElementById('correo_electronico');
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (correo.value.trim() === '') {
            document.getElementById('error-correo_electronico').textContent = 'El correo electrónico es obligatorio.';
            isValid = false;
        } else if (!emailRegex.test(correo.value)) {
            document.getElementById('error-correo_electronico').textContent = 'El formato del correo no es válido.';
            isValid = false;
        }
        
        // Teléfono
        const telefono = document.getElementById('numero_telefono');
        if (telefono.value.trim() === '') { // Revisar si está vacío
            document.getElementById('error-numero_telefono').textContent = 'El teléfono es obligatorio.';
            isValid = false;
        } else if (!/^\d+$/.test(telefono.value)) { // Revisar si son números
            document.getElementById('error-numero_telefono').textContent = 'El teléfono solo debe contener números.';
            isValid = false;
        }

        // Cuidador/Admin

        // Verificar si el bloque de campos para empleados está visible
        const camposCuidadorAdmin = document.getElementById('campos-cuidador-admin');

        // Si está visible validar sus campos
        if (camposCuidadorAdmin && camposCuidadorAdmin.style.display !== 'none') {

            // Fecha de Contratación
            const fechaContratacion = document.getElementById('fecha_contratacion');
            if (fechaContratacion.value.trim() === '') {
                document.getElementById('error-fecha_contratacion').textContent = 'La fecha de contratación es obligatoria.';
                isValid = false;
            }

            // Tipo de Contrato
            const tipoContrato = document.getElementById('tipo_contrato');
            if (tipoContrato.value.trim() === '') {
                document.getElementById('error-tipo_contrato').textContent = 'El tipo de contrato es obligatorio.';
                isValid = false;
            }

            // Contacto de Emergencia
            const contactoEmergencia = document.getElementById('contacto_emergencia');
            if (contactoEmergencia.value.trim() === '') {
                document.getElementById('error-contacto_emergencia').textContent = 'El contacto de emergencia es obligatorio.';
                isValid = false;
            }
        }

        // Familiar

        // Verificar si el bloque de campos para familiar está visible
        const camposFamiliar = document.getElementById('campos-familiar');

        // Si está visible validar su campo
        if (camposFamiliar && camposFamiliar.style.display !== 'none') {

            // Parentesco
            const parentesco = document.getElementById('parentesco');
            if (parentesco.value.trim() === '') {
                document.getElementById('error-parentesco').textContent = 'El parentesco es un campo obligatorio.';
                isValid = false;
            }
        }

        // Si todo es válido enviar el formulario
        if (isValid) {
            form.submit();
        }
    });
});
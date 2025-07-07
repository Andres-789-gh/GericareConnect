document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('registerForm');
    if (!form) {
        console.error("Formulario 'registerForm' no encontrado.");
        return;
    }

    form.addEventListener('submit', function (event) {
        // 1. Prevenir el envío automático para poder validar
        event.preventDefault();
        
        let isValid = true;
        
        // 2. Limpiar todos los mensajes de error anteriores
        document.querySelectorAll('.mensaje-error-campo').forEach(el => el.textContent = '');

        // --- VALIDACIONES GENERALES (CON MENSAJES DE ERROR PARA TODO) ---
        const nombre = document.getElementById('nombre');
        if (nombre.value.trim() === '') {
            document.getElementById('error-nombre').textContent = 'El nombre es obligatorio.';
            isValid = false;
        }

        const apellido = document.getElementById('apellido');
        if (apellido.value.trim() === '') {
            document.getElementById('error-apellido').textContent = 'El apellido es obligatorio.';
            isValid = false;
        }

        const direccion = document.getElementById('direccion');
        if (direccion.value.trim() === '') {
            document.getElementById('error-direccion').textContent = 'La dirección es obligatoria.';
            isValid = false;
        }

        const correo = document.getElementById('correo_electronico');
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (correo.value.trim() === '') {
            document.getElementById('error-correo_electronico').textContent = 'El correo electrónico es obligatorio.';
            isValid = false;
        } else if (!emailRegex.test(correo.value)) {
            document.getElementById('error-correo_electronico').textContent = 'El formato del correo no es válido.';
            isValid = false;
        }
        
        const telefono = document.getElementById('numero_telefono');
        if (telefono.value.trim() === '') {
            document.getElementById('error-numero_telefono').textContent = 'El teléfono es obligatorio.';
            isValid = false;
        } else if (!/^\d+$/.test(telefono.value)) {
            document.getElementById('error-numero_telefono').textContent = 'El teléfono solo debe contener números.';
            isValid = false;
        }

        // --- VALIDACIONES ESPECÍFICAS POR ROL ---
        const camposCuidadorAdmin = document.getElementById('campos-cuidador-admin');
        const camposFamiliar = document.getElementById('campos-familiar');

        // Validar campos de Empleado (solo si el bloque es visible)
        // Usamos window.getComputedStyle para una detección más fiable
        if (camposCuidadorAdmin && window.getComputedStyle(camposCuidadorAdmin).display !== 'none') {
            const fechaNacimiento = document.getElementById('fecha_nacimiento');
            if (!fechaNacimiento.value) {
                document.getElementById('error-fecha_nacimiento').textContent = 'La fecha de nacimiento es obligatoria.';
                isValid = false;
            }
            const fechaContratacion = document.getElementById('fecha_contratacion');
            if (!fechaContratacion.value) {
                document.getElementById('error-fecha_contratacion').textContent = 'La fecha de contratación es obligatoria.';
                isValid = false;
            }
            const tipoContrato = document.getElementById('tipo_contrato');
            if (tipoContrato.value.trim() === '') {
                document.getElementById('error-tipo_contrato').textContent = 'El tipo de contrato es obligatorio.';
                isValid = false;
            }
            const contactoEmergencia = document.getElementById('contacto_emergencia');
            if (contactoEmergencia.value.trim() === '') {
                document.getElementById('error-contacto_emergencia').textContent = 'El contacto de emergencia es obligatorio.';
                isValid = false;
            }
        }

        // Validar campo de Familiar (solo si el bloque es visible)
        if (camposFamiliar && window.getComputedStyle(camposFamiliar).display !== 'none') {
            const parentesco = document.getElementById('parentesco');
            if (parentesco.value.trim() === '') {
                document.getElementById('error-parentesco').textContent = 'El parentesco es un campo obligatorio.';
                isValid = false;
            }
        }

        // 3. Si después de todas las validaciones, isValid sigue siendo true, se envía el formulario
        if (isValid) {
            form.submit();
        } else {
            // Si algo falló, se notifica al usuario.
            alert('Por favor, revisa y corrige los errores marcados en el formulario.');
        }
    });
});
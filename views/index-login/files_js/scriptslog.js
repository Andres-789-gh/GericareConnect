document.addEventListener('DOMContentLoaded', function () {

    // 1. Configuración para las partículas del FONDO (blancas)
    particlesJS('particles-js-background', {
        "particles": {
            "number": { "value": 80, "density": { "enable": true, "value_area": 800 } },
            "color": { "value": "#ffffff" }, // Color blanco
            "shape": { "type": "circle" },
            "opacity": { "value": 0.5, "random": true },
            "size": { "value": 3, "random": true },
            "line_linked": { "enable": true, "distance": 150, "color": "#ffffff", "opacity": 0.4, "width": 1 },
            "move": { "enable": true, "speed": 4, "direction": "none", "out_mode": "out", "bounce": false }
        },
        "interactivity": {
            "detect_on": "window",
            "events": { "onhover": { "enable": true, "mode": "repulse" }, "resize": true }
        },
        "retina_detect": true
    });

    // 2. Configuración para las partículas de la TARJETA (multicolor)
    particlesJS('particles-js-card', {
        "particles": {
            "number": { "value": 50, "density": { "enable": true, "value_area": 800 } },
            // ¡Aquí la magia del multicolor!
            "color": { "value": ["#ff4a8d", "#3cff96", "#4ac7ff", "#fff04a"] },
            "shape": { "type": "circle" },
            "opacity": { "value": 0.8, "random": true },
            "size": { "value": 4, "random": true },
            "line_linked": { "enable": false }, // Sin líneas para un look más limpio
            "move": { "enable": true, "speed": 2, "direction": "bottom", "out_mode": "out", "bounce": false }
        },
        "interactivity": { "enable": false }, // Sin interactividad para no distraer
        "retina_detect": true
    });
    
    // Validación simple del formulario (opcional pero recomendada)
    const loginForm = document.getElementById("loginForm");
    if (loginForm) {
        loginForm.addEventListener("submit", function (event) {
            const tipoDocumento = document.querySelector('[name="tipo_documento"]').value;
            const documento = document.querySelector('[name="documento"]').value;
            if (tipoDocumento === "" || documento.trim() === "") {
                alert("Por favor, complete todos los campos.");
                event.preventDefault(); 
            }
        });
    }
});
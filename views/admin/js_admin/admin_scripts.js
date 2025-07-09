// views/admin/js_admin/admin_scripts.js

document.addEventListener('DOMContentLoaded', function () {
    // Inicializa Particles.js en el elemento con id 'particles-js'
    particlesJS({
        "particles": {
            "number": {
                "value": 80, // Cantidad de partículas
                "density": {
                    "enable": true,
                    "value_area": 800
                }
            },
            "color": {
                "value": "#ffffff" // Color de las partículas
            },
            "shape": {
                "type": "circle", // Forma de las partículas
            },
            "opacity": {
                "value": 0.5,
                "random": false
            },
            "size": {
                "value": 3, // Tamaño de las partículas
                "random": true
            },
            "line_linked": {
                "enable": true,
                "distance": 150, // Distancia para conectar líneas
                "color": "#ffffff",
                "opacity": 0.4,
                "width": 1
            },
            "move": {
                "enable": true,
                "speed": 6, // Velocidad de movimiento
                "direction": "none",
                "random": false,
                "straight": false,
                "out_mode": "out", // Comportamiento al salir del lienzo
                "bounce": false
            }
        },
        "interactivity": {
            "detect_on": "canvas",
            "events": {
                "onhover": {
                    "enable": true,
                    "mode": "repulse" // Efecto al pasar el mouse
                },
                "onclick": {
                    "enable": true,
                    "mode": "push" // Efecto al hacer clic
                },
                "resize": true
            },
            "modes": {
                "repulse": {
                    "distance": 100,
                    "duration": 0.4
                },
                "push": {
                    "particles_nb": 4 // Cantidad de partículas al hacer clic
                }
            }
        },
        "retina_detect": true
    });
});
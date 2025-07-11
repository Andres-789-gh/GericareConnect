<script src="../../libs/particles.js/particles.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // Inicializa las partículas del header
    if (document.getElementById('particles-js-cuidador')) {
        particlesJS('particles-js-cuidador', {
            "particles": { "number": { "value": 80, "density": { "enable": true, "value_area": 800 } }, "color": { "value": "#ffffff" }, "shape": { "type": "circle", }, "opacity": { "value": 0.5, "random": false }, "size": { "value": 3, "random": true }, "line_linked": { "enable": true, "distance": 150, "color": "#ffffff", "opacity": 0.4, "width": 1 }, "move": { "enable": true, "speed": 4, "direction": "none", "random": false, "straight": false, "out_mode": "out", "bounce": false, } }, "interactivity": { "detect_on": "canvas", "events": { "onhover": { "enable": true, "mode": "repulse" }, "onclick": { "enable": true, "mode": "push" }, "resize": true }, "modes": { "repulse": { "distance": 100, "duration": 0.4 }, "push": { "particles_nb": 4 } } }, "retina_detect": true
        });
    }

    // Funcionalidad del menú desplegable del usuario
    const userInfo = document.querySelector('.user-info');
    if (userInfo) {
        userInfo.addEventListener('click', function(event) {
            event.stopPropagation();
            this.querySelector('.dropdown-menu').classList.toggle('show');
        });
    }
    // Cierra el menú si se hace clic fuera
    window.addEventListener('click', function() {
        const openDropdown = document.querySelector('.dropdown-menu.show');
        if (openDropdown) {
            openDropdown.classList.remove('show');
        }
    });

});
</script>

</body>
</html>
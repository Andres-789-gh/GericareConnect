document.addEventListener('DOMContentLoaded', function() {
    const rows = document.querySelectorAll('.animate-row');
    rows.forEach((row, index) => {
        row.style.opacity = 0;
        row.classList.add('animate__animated', 'animate__fadeInUp');
        row.style.animationDelay = `${index * 0.05}s`;
        row.style.opacity = 1;
    });
    if (document.getElementById('particles-js')) {
        particlesJS('particles-js', {"particles": {"number": {"value": 50,"density": {"enable": true,"value_area": 800}},"color": {"value": "#007bff"},"shape": {"type": "circle"},"opacity": {"value": 0.3,"random": true},"size": {"value": 3,"random": true},"line_linked": {"enable": false},"move": {"enable": true,"speed": 1,"direction": "none","random": true,"straight": false,"out_mode": "out"}},"interactivity": {"detect_on": "canvas","events": {"onhover": {"enable": false},"onclick": {"enable": false},"resize": true}}});
    }
});
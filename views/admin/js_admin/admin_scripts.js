document.addEventListener('DOMContentLoaded', function() {
    const rows = document.querySelectorAll('.animate-row');
    rows.forEach((row, index) => {
        row.style.opacity = 0;
        row.classList.add('animate__animated', 'animate__fadeInUp');
        row.style.animationDelay = `${index * 0.05}s`;
        row.style.opacity = 1;
    });
});
function cargarFamiliares(busqueda = '') {
    const familiarList = document.getElementById('familiar-list');
    familiarList.innerHTML = '<li class="paciente-item cargando">Cargando familiares...</li>';

    let url = 'obtener_familiares.php';
    if (busqueda) {
        url += `?buscar-familiar=${encodeURIComponent(busqueda)}`;
    }

    fetch(url)
        .then(response => response.json())
        .then(data => {
            familiarList.innerHTML = '';
            if (data && data.length > 0) {
                data.forEach(familiar => {
                    const listItem = document.createElement('li');
                    listItem.classList.add('paciente-item', 'animated', 'fadeInUp');
                    listItem.innerHTML = `
                        ${familiar.nombres} ${familiar.apellidos} - Activo
                        <span class="menu-icon">
                            <i class="fas fa-bars"></i>
                        </span>
                    `;
                    familiarList.appendChild(listItem);
                });
            }
        })
        .catch(error => {
            console.error('Error al cargar los familiares:', error);
            familiarList.innerHTML = '<li class="paciente-item error">Error al cargar los familiares.</li>';
        });
}
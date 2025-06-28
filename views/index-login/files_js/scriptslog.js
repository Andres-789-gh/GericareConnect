document.addEventListener("DOMContentLoaded", function () {
    const loginForm = document.getElementById("loginForm");

    if (loginForm) {
        loginForm.addEventListener("submit", function (event) {
            const tipoDocumento = document.getElementById("tipo_documento").value;
            const documento = document.getElementById("documento").value;

            if (tipoDocumento === "" || documento.trim() === "") {
                alert("Por favor, complete todos los campos.");
                event.preventDefault(); // Evita que se envíe el formulario
                return;
            }
        });
    }
});


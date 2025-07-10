<?php
/*Inicia la sesión para poder leer las variables de sesión como $_SESSION['error_login'].*/
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - GeriCare Connect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../files_css/index.css">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center align-items-center vh-100">
            <div class="col-md-6 col-lg-4">
                <div class="card shadow-sm p-4">
                    <div class="text-center mb-4">
                        <img src="../../imagenes/Geri_Logo-..png" alt="Logo Gericare" style="width: 100px;">
                        <h2 class="mt-3">Iniciar Sesión</h2>
                    </div>

                    <?php
                    /*
                    1. "isset" comprueba si la variable $_SESSION['error_login'] existe.
                    2. Si existe, imprime el mensaje de error dentro de un div con estilo.
                    3. "unset" borra la variable para que el error no se muestre de nuevo si recargas la página.
                    */
                    if (isset($_SESSION['error_login'])) {
                        /* Se aplica la clase "alert alert-danger" de Bootstrap al mensaje de error */
                        echo "<div class='alert alert-danger'>" . htmlspecialchars($_SESSION['error_login']) . "</div>";
                        unset($_SESSION['error_login']);
                    }
                    ?>

                    <div id="error-container" class="error-box" style="display: none;">
                        <p id="error-message" class="error-msg"></p>
                    </div>

                    <form id="loginForm" action="../../../controllers/index-login/index_controller.php" method="POST">
                        <select name="tipo_documento" class="form-select mb-3" required>
                            <option value="" disabled selected>Selecciona el tipo de documento</option>
                            <option value="CC">Cédula de Ciudadanía</option>
                            <option value="CE">Cédula de Extranjería</option>
                            <option value="PA">Pasaporte</option>
                        </select>
                        <input type="text" name="documento" placeholder="Número de Documento" class="form-control mb-3" required>
                        <div class="password-container mb-3">
                            <input type="password" name="password" placeholder="Contraseña" required id="passwordInput" class="form-control">
                            <i class="fas fa-eye-slash text-secondary toggle-password" id="togglePassword"></i>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Ingresar</button>
                    </form>
                    <p class="alert-link">¿No tienes cuenta? <a href="../../familiar/html_familiar/registro_familiar_view.php">Regístrate aquí</a>.</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        const urlParams = new URLSearchParams(window.location.search);
        const error = urlParams.get('error');
        const success = urlParams.get('success');
        const errorContainer = document.getElementById('error-container');
        const errorMessageElement = document.getElementById('error-message');

        if (error) {
            errorMessageElement.textContent = decodeURIComponent(error);
            errorContainer.style.display = 'block';
            errorContainer.classList.remove('success-box');
            errorContainer.classList.add('error-box');
        } else if (success) {
            errorMessageElement.textContent = decodeURIComponent(success);
            errorContainer.style.display = 'block';
            errorContainer.classList.remove('error-box');
            errorContainer.classList.add('success-box'); 
        }

        const passwordInput = document.getElementById('passwordInput');
        const togglePassword = document.getElementById('togglePassword');

        if (togglePassword) {
            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            });
        }
    </script>
     <style>
        .success-box {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            padding: 10px 15px;
            margin-bottom: 15px;
            border-radius: 4px;
            text-align: center;
        }
        .success-msg {
            margin: 0;
            padding: 0;
            font-weight: bold;
            color: #155724;
        }
    </style>
</body>
</html>
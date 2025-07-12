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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../files_css/styleslogin1.css"> 
</head>
<body>
    <div id="particles-js-background"></div>

    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg animate__animated animate__fadeIn">
                
                <div id="particles-js-card"></div>

                <div class="card-body p-4 p-md-5">
                    <div class="text-center mb-4">
                        <img src="../../imagenes/Geri_Logo-..png"  alt="GeriCare Connect Logo" class="logo-spin" style="width: 120px;">
                        
                        <h2 class="mt-3">Iniciar Sesión</h2>
                    </div>

                    <?php
                    if (isset($_SESSION['error_login'])) {
                        echo "<div class='alert alert-danger'>" . htmlspecialchars($_SESSION['error_login']) . "</div>";
                        unset($_SESSION['error_login']);
                    }
                    ?>

                    <form id="loginForm" action="../../../controllers/index-login/index_controller.php" method="POST">
                        <select name="tipo_documento" class="form-select mb-3" required>
                            <option value="" disabled selected>Tipo de documento</option>
                            <option value="CC">Cédula de Ciudadanía</option>
                            <option value="CE">Cédula de Extranjería</option>
                            <option value="PA">Pasaporte</option>
                        </select>
                        
                        <input type="text" name="documento" placeholder="Número de Documento" class="form-control mb-3" required>
                        
                        <div class="input-group mb-3">
                            <input type="password" name="password" placeholder="Contraseña" required id="passwordInput" class="form-control">
                            <span class="input-group-text">
                                <i class="fas fa-eye-slash" id="togglePassword" style="cursor: pointer;"></i>
                            </span>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100">Ingresar</button>
                    </form>
                    
                    <p class="mt-3 text-center">¿No tienes cuenta? <a href="../../familiar/html_familiar/registro_familiar_view.php">Regístrate aquí</a>.</p>
                </div>
            </div>
        </div>
    </div>

    <script src="../../libs/particles.js/particles.min.js"></script>
    <script src="../../index-login/files_js/scriptslog.js"></script>
    <script>
        // Script para mostrar/ocultar contraseña
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
</body>
</html>
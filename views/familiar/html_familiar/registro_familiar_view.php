<?php
session_start();
$mensaje = $_SESSION['mensaje'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['mensaje'], $_SESSION['error']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - GeriCare Connect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background-image: url('../../imagenes/loginimg.jpg');
            background-position: center;
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }
        /* Clase para el botón morado personalizado */
        .btn-custom-purple {
            background-color: #6200ea;
            border-color: #6200ea;
            color: white;
            border-radius: 20px; /* Bordes redondeados */
            font-weight: bold;
            transition: background-color 0.3s ease-in-out, transform 0.2s;
        }
        .btn-custom-purple:hover {
            background-color: #3700b3; /* Color morado más oscuro al pasar el mouse */
            border-color: #3700b3;
            color: white;
            transform: scale(1.03);
        }
        .form-control, .form-select {
            border-radius: 8px;
            height: 48px;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow-sm p-4">
                    <div class="text-center mb-4">
                        <img src="../../imagenes/Geri_Logo-..png" alt="Logo Gericare" style="width: 100px;">
                        <h2 class="mt-3">Registro de Familiar</h2>
                    </div>

                    <?php if ($mensaje): ?>
                        <div class="alert alert-success" role="alert"><?= htmlspecialchars($mensaje) ?></div>
                    <?php endif; ?>
                    <?php if ($error): ?>
                        <div class="alert alert-danger" role="alert"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>

                    <form id="registerForm" action="../../../controllers/familiar/registro_familiar_controller.php" method="POST">
                        <div class="row g-3">
                            <div class="col-12">
                                <select name="tipo_documento" class="form-select" required>
                                    <option value="" disabled selected>Tipo de documento</option>
                                    <option value="CC">Cédula de Ciudadanía</option>
                                    <option value="CE">Cédula de Extranjería</option>
                                    <option value="PA">Pasaporte</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <input type="number" name="documento_identificacion" class="form-control" placeholder="N° de documento" required>
                            </div>
                            <div class="col-md-6">
                                <input type="text" name="nombre" class="form-control" placeholder="Nombre" required>
                            </div>
                            <div class="col-md-6">
                                <input type="text" name="apellido" class="form-control" placeholder="Apellido" required>
                            </div>
                            <div class="col-12">
                                <input type="text" name="direccion" class="form-control" placeholder="Dirección" required>
                            </div>
                            <div class="col-12">
                                <input type="email" name="correo_electronico" class="form-control" placeholder="Correo electrónico" required>
                            </div>
                            <div class="col-12">
                                <input type="number" name="numero_telefono" class="form-control" placeholder="Número de teléfono">
                            </div>
                            <div class="col-12">
                                <input type="text" name="parentesco" class="form-control" placeholder="Parentesco" required>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-custom-purple w-100 py-2">Registrarse</button>
                            </div>
                        </div>
                    </form>
                    <p class="mt-3 text-center">¿Ya tienes una cuenta? <a href="../../index-login/htmls/index.php">Iniciar sesión</a></p>
                </div>
            </div>
        </div>
    </div>
    <script src="../../index-login/files_js/scripts.js"></script>
</body>
</html>

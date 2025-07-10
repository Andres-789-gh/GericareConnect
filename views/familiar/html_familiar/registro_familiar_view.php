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
    <link rel="stylesheet" href="../../index-login/files_css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="register-container">
        <img src="../../imagenes/Geri_Logo-..png" alt="Logo" class="logo">
        <img src="../../imagenes/Geri_Logo-.png" alt="Logo" class="logo2">
        <h2>Registro de Familiar</h2>

        <!-- Mensajes -->
        <?php if ($mensaje): ?>
            <div class="mensaje-exito"><?= htmlspecialchars($mensaje) ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="mensaje-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form id="registerForm" action="../../../controllers/familiar/registro_familiar_controller.php" method="POST">
            <div class="form-grid">
                <!-- Datos generales -->
                <select name="tipo_documento" required>
                    <option value="">Tipo de documento</option>
                    <option value="CC">Cédula de Ciudadanía</option>
                    <option value="CE">Cédula de Extranjería</option>
                    <option value="PA">Pasaporte</option>
                </select>

                <input type="number" name="documento_identificacion" placeholder="N° de documento" required>
                <input type="text" name="nombre" placeholder="Nombre" required>
                <input type="text" name="apellido" placeholder="Apellido" required>

                <input type="text" name="direccion" placeholder="Dirección" required>
                <input type="email" name="correo_electronico" placeholder="Correo electrónico" required>
                <input type="number" name="numero_telefono" placeholder="Número de teléfono">

                <!-- Parentesco -->
                <input type="text" name="parentesco" placeholder="Parentesco con el paciente" required>
            </div>

            <!-- Botón -->
            <div id="boton-registro">
                <button type="submit">Registrarse</button>
            </div>
        </form>

        <p>¿Ya tienes una cuenta? <a href="../../index-login/htmls/index.php">Iniciar sesión</a></p>
    </div>
    <script src="../../index-login/files_js/scripts.js"></script>
</body>
</html>

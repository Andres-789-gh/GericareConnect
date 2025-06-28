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
    <link rel="stylesheet" href="../files_css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="register-container">
        <img src="../../imagenes/Geri_Logo-..png" alt="Logo" class="logo">
        <img src="../../imagenes/Geri_Logo-.png" alt="Logo" class="logo2">
        <h2>Crea una cuenta</h2>

        <!-- Contenedor de errores -->
        <div id="error-container" class="error-box" style="display: none;">
            <p id="error-message" class="error-msg"></p>
        </div>

        <!-- Mensajes -->
        <?php if ($mensaje): ?>
            <div class="mensaje-exito"><?= htmlspecialchars($mensaje) ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="mensaje-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>


        <form id="registerForm" action="../../../controllers/index-login/registro_controller.php" method="POST">

            <div class="form-grid">

                <!--Campos generales obligatorios-->
                <!-- Tipo de documento -->
                <select name="tipo_documento" id="tipo_documento" required>
                    <option value="">Seleccione tipo de documento</option>
                    <option value="CC">Cédula de Ciudadanía</option>
                    <option value="CE">Cédula de Extranjería</option>
                    <option value="PA">Pasaporte</option>
                </select>
                <!-- Datos generales -->
                <input type="number" name="documento_identificacion" id="documento_identificacion" placeholder="N° de documento" required>
                <input type="text" name="nombre" id="nombre" placeholder="Nombre" required>
                <input type="text" name="apellido" id="apellido" placeholder="Apellido" required>
                <!-- Fecha de nacimiento con label -->
                <label for="fecha_nacimiento" class="form-label">Fecha de nacimiento</label>
                <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" required>

                <input type="text" name="direccion" id="direccion" placeholder="Dirección" required>
                <input type="email" name="correo_electronico" id="correo_electronico" placeholder="Correo electrónico" required>

                <input type="text" name="numero_telefono" id="numero_telefono" placeholder="Número de teléfono">

                <!--Selección de roles-->
                <div class="roles-column-buttons">
                    <label class="checkbox-rol">
                        <input type="checkbox" name="roles[]" value="Administrador" id="rol-administrador">
                        <span>Administrador</span>
                    </label>
                    <label class="checkbox-rol">
                        <input type="checkbox" name="roles[]" value="Cuidador" id="rol-cuidador">
                        <span>Cuidador</span>
                    </label>
                    <label class="checkbox-rol">
                        <input type="checkbox" name="roles[]" value="Familiar" id="rol-familiar">
                        <span>Familiar</span>
                    </label>
                </div>

                <!--Campos especificos opcionales dependiendo del rol-->

                <!-- Cuidador/Admin -->
                <div id="campos-cuidador-admin" class="campos-rol form-grid">
                    <label for="fecha_nacimiento" class="form-label">Fecha de contratación</label>
                    <input type="date" name="fecha_contratacion" id="fecha_contratacion">
                    <input type="text" name="tipo_contrato" id="tipo_contrato" placeholder="Tipo de contrato">
                    <input type="text" name="contacto_emergencia" id="contacto_emergencia" placeholder="Contacto de emergencia">
                </div>

                <!-- Familiar -->
                <div id="campos-familiar" class="campos-rol form-grid">
                    <input type="text" name="parentesco" id="parentesco" placeholder="Parentesco con el paciente">
                </div>

                <!--Botón-->
                <div id="boton-registro">
                    <button type="submit">Registrarse</button>
                </div>

            </div>
        </form>
        <p>¿Ya tienes una cuenta? <a href="index.html">Iniciar sesión</a></p>
    </div>
    <script src="../files_js/scripts.js"></script>
</body>
</html>
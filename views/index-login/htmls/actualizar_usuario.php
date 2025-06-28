<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$rol = $datosUsuario['roles'];
$isFamiliar = $rol === 'Familiar';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Actualizar Usuario</title>
    <link rel="stylesheet" href="/gericare/GericareConnect/views/index-login/files_css/styles.css">
    <script src="/gericare/GericareConnect/views/index-login/files_js/scripts.js" defer></script>
</head>
<body>
    <div class="register-container">
        <img src="/GericareConnect/views/imagenes/Geri_Logo-..png" alt="Logo" class="logo">
        <img src="/GericareConnect/views/imagenes/Geri_Logo-.png" alt="Logo" class="logo2">
        <h2>Actualizar Usuario</h2>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="mensaje-error"><?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <form id="registerForm" action="/GericareConnect/controllers/index-login/actualizar_controller.php" method="POST">
            <input type="hidden" name="id_usuario" value="<?= htmlspecialchars($datosUsuario['id_usuario']) ?>">
            <input type="hidden" name="rol" value="<?= htmlspecialchars($rol) ?>">

            <div class="form-grid">

                <label for="tipo_documento" class="form-label">Tipo de documento</label>
                <select name="tipo_documento" id="tipo_documento" required>
                    <option value="">Seleccione tipo de documento</option>
                    <option value="CC" <?= $datosUsuario['tipo_documento'] == 'CC' ? 'selected' : '' ?>>Cédula de Ciudadanía</option>
                    <option value="CE" <?= $datosUsuario['tipo_documento'] == 'CE' ? 'selected' : '' ?>>Cédula de Extranjería</option>
                    <option value="PA" <?= $datosUsuario['tipo_documento'] == 'PA' ? 'selected' : '' ?>>Pasaporte</option>
                </select>

                <label for="documento_identificacion" class="form-label">Número de documento</label>
                <input type="number" name="documento_identificacion" id="documento_identificacion" value="<?= htmlspecialchars($datosUsuario['documento_identificacion']) ?>" required>

                <label for="nombre" class="form-label">Nombre</label>
                <input type="text" name="nombre" id="nombre" value="<?= htmlspecialchars($datosUsuario['nombre']) ?>" required>

                <label for="apellido" class="form-label">Apellido</label>
                <input type="text" name="apellido" id="apellido" value="<?= htmlspecialchars($datosUsuario['apellido']) ?>" required>

                <label for="fecha_nacimiento" class="form-label">Fecha de nacimiento</label>
                <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" value="<?= htmlspecialchars($datosUsuario['fecha_nacimiento']) ?>" required>

                <label for="direccion" class="form-label">Dirección</label>
                <input type="text" name="direccion" id="direccion" value="<?= htmlspecialchars($datosUsuario['direccion']) ?>" required>

                <label for="correo_electronico" class="form-label">Correo electrónico</label>
                <input type="email" name="correo_electronico" id="correo_electronico" value="<?= htmlspecialchars($datosUsuario['correo_electronico']) ?>" required>

                <label for="numero_telefono" class="form-label">Número de teléfono</label>
                <input type="text" name="numero_telefono" id="numero_telefono" value="<?= htmlspecialchars($datosUsuario['numero_telefono'] ?? '') ?>">

                <!-- Campos para Cuidador y Administrador -->
                <div id="campos-cuidador-admin" class="campos-rol form-grid" style="<?= $isFamiliar ? 'display:none;' : '' ?>">

                    <label for="fecha_contratacion" class="form-label">Fecha de contratación</label>
                    <input type="date" name="fecha_contratacion" id="fecha_contratacion" value="<?= htmlspecialchars($datosUsuario['fecha_contratacion'] ?? '') ?>">

                    <label for="tipo_contrato" class="form-label">Tipo de contrato</label>
                    <input type="text" name="tipo_contrato" id="tipo_contrato" value="<?= htmlspecialchars($datosUsuario['tipo_contrato'] ?? '') ?>">

                    <label for="contacto_emergencia" class="form-label">Contacto de emergencia</label>
                    <input type="text" name="contacto_emergencia" id="contacto_emergencia" value="<?= htmlspecialchars($datosUsuario['contacto_emergencia'] ?? '') ?>">

                </div>

                <!-- Campo para Familiar -->
                <div id="campos-familiar" class="campos-rol form-grid" style="<?= !$isFamiliar ? 'display:none;' : '' ?>">
                    <label for="parentesco" class="form-label">Parentesco con el paciente</label>
                    <input type="text" name="parentesco" id="parentesco" value="<?= htmlspecialchars($datosUsuario['parentesco'] ?? '') ?>">
                </div>

                <div id="boton-registro">
                    <button type="submit">Actualizar</button>
                    <a href="/GericareConnect/views/admin/html_admin/admin_pacientes.php" class="cancel-button">Cancelar</a>
                </div>
            </div>
        </form>
    </div>
</body>
</html>

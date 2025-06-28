<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($datosUsuario)) {
    header("Location: listar_usuarios.php");
    exit;
}
$rolesActuales = explode(',', $datosUsuario['roles']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Actualizar Usuario</title>
    <link rel="stylesheet" href="/gericare_connect/views/index-login/files_css/styles.css">
    <script src="/gericare_connect/views/index-login/files_js/scripts.js" defer></script>
</head>
<body>
    <div class="register-container">
        <img src="/gericare_connect/views/imagenes/Geri_Logo-..png" alt="Logo" class="logo">
        <img src="/gericare_connect/views/imagenes/Geri_Logo-.png" alt="Logo" class="logo2">
        <h2>Actualizar Usuario</h2>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="mensaje-error"><?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <form id="registerForm" action="../../../controllers/index-login/actualizar_controller.php" method="POST">
            <input type="hidden" name="id_usuario" value="<?= htmlspecialchars($datosUsuario['id_usuario']) ?>">

            <div class="form-grid">
                <select name="tipo_documento" required>
                    <option value="">Seleccione tipo de documento</option>
                    <option value="CC" <?= $datosUsuario['tipo_documento'] == 'CC' ? 'selected' : '' ?>>Cédula de Ciudadanía</option>
                    <option value="CE" <?= $datosUsuario['tipo_documento'] == 'CE' ? 'selected' : '' ?>>Cédula de Extranjería</option>
                    <option value="PA" <?= $datosUsuario['tipo_documento'] == 'PA' ? 'selected' : '' ?>>Pasaporte</option>
                </select>

                <input type="number" name="documento_identificacion" value="<?= htmlspecialchars($datosUsuario['documento_identificacion']) ?>" required>
                <input type="text" name="nombre" value="<?= htmlspecialchars($datosUsuario['nombre']) ?>" required>
                <input type="text" name="apellido" value="<?= htmlspecialchars($datosUsuario['apellido']) ?>" required>

                <label for="fecha_nacimiento" class="form-label">Fecha de nacimiento</label>
                <input type="date" name="fecha_nacimiento" value="<?= htmlspecialchars($datosUsuario['fecha_nacimiento']) ?>" required>

                <input type="text" name="direccion" value="<?= htmlspecialchars($datosUsuario['direccion']) ?>" required>
                <input type="email" name="correo_electronico" value="<?= htmlspecialchars($datosUsuario['correo_electronico']) ?>" required>
                <input type="text" name="numero_telefono" value="<?= htmlspecialchars($datosUsuario['numero_telefono'] ?? '') ?>">

                <!-- Roles -->
                <div class="roles-column-buttons">
                    <?php
                    $todosLosRoles = ['Administrador', 'Cuidador', 'Familiar'];
                    foreach ($todosLosRoles as $rol) {
                        $checked = in_array($rol, $rolesActuales) ? 'checked' : '';
                        $id = "rol-" . strtolower($rol);
                        echo "
                        <label class='checkbox-rol'>
                            <input type='checkbox' name='roles[]' value='$rol' id='$id' $checked>
                            <span>$rol</span>
                        </label>";
                    }
                    ?>
                </div>

                <!-- Campos específicos -->
                <div id="campos-cuidador-admin" class="campos-rol form-grid">
                    <label for="fecha_contratacion" class="form-label">Fecha de contratación</label>
                    <input type="date" name="fecha_contratacion" id="fecha_contratacion" value="<?= htmlspecialchars($datosUsuario['fecha_contratacion'] ?? '') ?>">
                    <input type="text" name="tipo_contrato" id="tipo_contrato" placeholder="Tipo de contrato" value="<?= htmlspecialchars($datosUsuario['tipo_contrato'] ?? '') ?>">
                    <input type="text" name="contacto_emergencia" id="contacto_emergencia" placeholder="Contacto de emergencia" value="<?= htmlspecialchars($datosUsuario['contacto_emergencia'] ?? '') ?>">
                </div>

                <div id="campos-familiar" class="campos-rol form-grid">
                    <input type="text" name="parentesco" id="parentesco" placeholder="Parentesco con el paciente" value="<?= htmlspecialchars($datosUsuario['parentesco'] ?? '') ?>">
                </div>

                <div id="boton-registro">
                    <button type="submit">Actualizar</button>
                    <a href="listar_usuarios.php" class="cancel-button">Cancelar</a>
                </div>
            </div>
        </form>
    </div>
</body>
</html>

<?php
session_start();
// Asegurarse de que solo el administrador pueda ver esta página
if (!isset($_SESSION['nombre_rol']) || $_SESSION['nombre_rol'] !== 'Administrador') {
    header("Location: /GericareConnect/views/index-login/htmls/index.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Nuevo Empleado</title>
    <link rel="stylesheet" href="/GericareConnect/views/index-login/files_css/styles.css">
</head>
<body>
<div class="register-container">
    <img src="/GericareConnect/views/imagenes/Geri_Logo-..png" alt="Logo" class="logo">
    <img src="/GericareConnect/views/imagenes/Geri_Logo-.png" alt="Logo" class="logo2">
    <h2>Registrar Nuevo Empleado</h2>

    <!-- Contenedor para mensajes de error/exito -->
    <?php
    if (isset($_SESSION['error_registro'])) {
        echo '<div class="mensaje-error">' . $_SESSION['error_registro'] . '</div>';
        unset($_SESSION['error_registro']);
    }
    ?>

    <form id="registerForm" action="../../../controllers/admin/registrar_empleado_controller.php" method="POST" novalidate>
        <div class="form-grid">
            
            <label for="nombre_rol" class="form-label">Rol del Empleado</label>
            <select name="nombre_rol" id="nombre_rol" required>
                <option value="">Seleccione un rol...</option>
                <option value="Administrador">Administrador</option>
                <option value="Cuidador">Cuidador</option>
            </select>
            <small class="mensaje-error-campo"></small>

            <label for="nombre" class="form-label">Nombre</label>
            <input type="text" name="nombre" id="nombre" required>
            <small class="mensaje-error-campo"></small>

            <label for="apellido" class="form-label">Apellido</label>
            <input type="text" name="apellido" id="apellido" required>
            <small class="mensaje-error-campo"></small>

            <label for="tipo_documento" class="form-label">Tipo de Documento</label>
            <select name="tipo_documento" id="tipo_documento" required>
                <option value="">Seleccione...</option>
                <option value="CC">Cédula de Ciudadanía</option>
                <option value="CE">Cédula de Extranjería</option>
                <option value="PA">Pasaporte</option>
            </select>
            <small class="mensaje-error-campo"></small>

            <label for="documento_identificacion" class="form-label">Número de Documento</label>
            <input type="number" name="documento_identificacion" id="documento_identificacion" required>
            <small class="mensaje-error-campo"></small>

            <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento</label>
            <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" required>
            <small class="mensaje-error-campo"></small>

            <label for="direccion" class="form-label">Dirección</label>
            <input type="text" name="direccion" id="direccion" required>
            <small class="mensaje-error-campo"></small>

            <label for="correo_electronico" class="form-label">Correo Electrónico</label>
            <input type="email" name="correo_electronico" id="correo_electronico" required>
            <small class="mensaje-error-campo"></small>

            <label for="numero_telefono" class="form-label">Número de Teléfono</label>
            <input type="text" name="numero_telefono" id="numero_telefono" required>
            <small class="mensaje-error-campo"></small>

            <label for="fecha_contratacion" class="form-label">Fecha de Contratación</label>
            <input type="date" name="fecha_contratacion" id="fecha_contratacion" required>
            <small class="mensaje-error-campo"></small>

            <label for="tipo_contrato" class="form-label">Tipo de Contrato</label>
            <input type="text" name="tipo_contrato" id="tipo_contrato" required>
            <small class="mensaje-error-campo"></small>

            <label for="contacto_emergencia" class="form-label">Contacto de Emergencia</label>
            <input type="text" name="contacto_emergencia" id="contacto_emergencia" required>
            <small class="mensaje-error-campo"></small>
            
            <label for="contrasena" class="form-label">Contraseña</label>
            <input type="password" name="contrasena" id="contrasena" required>
            <small class="mensaje-error-campo"></small>

            <label for="confirmar_contrasena" class="form-label">Confirmar Contraseña</label>
            <input type="password" name="confirmar_contrasena" id="confirmar_contrasena" required>
            <small class="mensaje-error-campo"></small>

        </div>
        <div id="boton-registro">
            <button type="submit">Registrar Empleado</button>
            <a href="admin_pacientes.php" class="cancel-button">Cancelar</a>
        </div>
    </form>
</div>
</body>
</html>

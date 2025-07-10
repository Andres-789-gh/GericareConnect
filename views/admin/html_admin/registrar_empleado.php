<?php
require_once __DIR__ . '/../../../controllers/auth/verificar_sesion.php';
verificarAcceso(['Administrador']);
// Asegurarse de que solo el administrador pueda ver esta página
if (!isset($_SESSION['nombre_rol']) || $_SESSION['nombre_rol'] !== 'Administrador') {
    // Redirigir si no es administrador
    header("Location: /GericareConnect/views/index-login/htmls/index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Empleado - GeriCare Connect</title>
    <link rel="stylesheet" href="/GericareConnect/views/index-login/files_css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
<div class="register-container">
    <img src="/GericareConnect/views/imagenes/Geri_Logo-..png" alt="Logo" class="logo">
    <img src="/GericareConnect/views/imagenes/Geri_Logo-.png" alt="Logo" class="logo2">
    <h2>Registrar Nuevo Empleado</h2>

    <?php
    if (isset($_SESSION['error_registro'])) {
        echo '<div class="mensaje-error" style="background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 15px;">' . htmlspecialchars($_SESSION['error_registro']) . '</div>';
        unset($_SESSION['error_registro']);
    }
    ?>

    <form id="registerForm" action="../../../controllers/admin/registrar_empleado_controller.php" method="POST">
        <div class="form-grid">
            
            <select name="nombre_rol" required>
                <option value="" disabled selected>Rol del Empleado</option>
                <option value="Administrador">Administrador</option>
                <option value="Cuidador">Cuidador</option>
            </select>

            <input type="text" name="nombre" placeholder="Nombre" required>
            <input type="text" name="apellido" placeholder="Apellido" required>

            <select name="tipo_documento" required>
                <option value="" disabled selected>Tipo de documento</option>
                <option value="CC">Cédula de Ciudadanía</option>
                <option value="CE">Cédula de Extranjería</option>
                <option value="PA">Pasaporte</option>
            </select>

            <input type="number" name="documento_identificacion" placeholder="Número de Documento" required>
            
            <label for="fecha_nacimiento" style="margin-bottom: -10px; font-size: 0.9em; color: #555;">Fecha de Nacimiento</label>
            <input type="date" name="fecha_nacimiento" required>

            <input type="text" name="direccion" placeholder="Dirección" required>
            <input type="email" name="correo_electronico" placeholder="Correo Electrónico" required>
            <input type="number" name="numero_telefono" placeholder="Número de Teléfono" required>

            <label for="fecha_contratacion" style="margin-bottom: -10px; font-size: 0.9em; color: #555;">Fecha de Contratación</label>
            <input type="date" name="fecha_contratacion" required>

            <input type="text" name="tipo_contrato" placeholder="Tipo de Contrato" required>
            <input type="number" name="contacto_emergencia" placeholder="Contacto de Emergencia" required>

        </div>
        <div id="boton-registro" style="margin-top: 20px;">
            <button type="submit">Registrar Empleado</button>
            <a href="admin_pacientes.php" class="cancel-button" style="margin-top: 10px;">Cancelar</a>
        </div>
    </form>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const registerForm = document.getElementById('registerForm');
        
        if (registerForm) {
            registerForm.addEventListener('submit', function() {
                const submitButton = registerForm.querySelector('button[type="submit"]');
                if (submitButton) {
                    submitButton.disabled = true;
                    submitButton.innerHTML = 'Procesando...';
                }
            });
        }
    });
</script>
</body>
</html>
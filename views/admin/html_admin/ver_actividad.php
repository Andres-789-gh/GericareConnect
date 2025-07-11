<?php
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Detalles de Actividad</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="/GericareConnect/views/admin/css_admin/historia_clinica_form.css">
</head>
<body>
    <?php
    // Inicia la sesión si aún no está iniciada para poder leer los mensajes.
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    // Revisa si hay un mensaje de éxito y lo muestra.
    if (isset($_SESSION['mensaje'])) {
        echo '<div class="alert alert-success">' . $_SESSION['mensaje'] . '</div>';
        // Limpia el mensaje para que no aparezca de nuevo si se recarga la página.
        unset($_SESSION['mensaje']);
    }

    // Revisa si hay un mensaje de error y lo muestra.
    if (isset($_SESSION['error'])) {
        echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
        // Limpia el mensaje.
        unset($_SESSION['error']);
    }
    ?>
    <div class="form-container">
        <h2 style="color: #007bff; text-align: center;">Ver Detalles Actividad</h2>

        <form>
            <input type="hidden" name="id_actividad" value="<?= htmlspecialchars($actividad['id_actividad'] ?? '') ?>">

            <div class="form-grid">
                <div class="form-group">
                    <label>Paciente</label>
                    <input type="text" value="<?= htmlspecialchars($actividad['nombre_paciente'] ?? 'No asignado') ?>" disabled>
                </div>

                <div class="form-group"><label>Tipo de Actividad</label><input type="text" value="<?= htmlspecialchars($actividad['tipo_actividad'] ?? '') ?>" required disabled></div>
                <div class="form-group"><label>Fecha</label><input type="date" value="<?= htmlspecialchars($actividad['fecha_actividad'] ?? '') ?>" required disabled></div>
                <div class="form-group"><label>Hora Inicio</label><input type="time" value="<?= htmlspecialchars($actividad['hora_inicio'] ?? '') ?>" disabled></div>
                <div class="form-group"><label>Hora Fin</label><input type="time" value="<?= htmlspecialchars($actividad['hora_fin'] ?? '') ?>" disabled></div>
                
                <div class="form-group full-width"><label>Descripción</label><textarea rows="4" disabled><?= htmlspecialchars($actividad['descripcion_actividad'] ?? '') ?></textarea></div>
            </div>

            <div class="form-actions">
                <a href="<?= htmlspecialchars($url_volver) ?>" class="btn btn-secondary">Volver</a>
            </div>
        </form>
    </div>
</body>
</html>
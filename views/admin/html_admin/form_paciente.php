<?php
session_start();
// Aquí podrías incluir lógica para cargar pacientes y cuidadores en los <select>
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asignar Actividad - GericareConnect</title>
    
    <link rel="stylesheet" href="../css_admin/admin_main.css">
    <link rel="stylesheet" href="../css_admin/form_styles.css"> </head>
<body>
    <div class="container">
        <header>
            <div class="header-content">
                <div class="logo-container">
                    <img src="../../../../public/img/logo.png" alt="GericareConnect Logo" class="logo">
                    <h1>GericareConnect</h1>
                </div>
                <div class="user-info">
                    <span>Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre_admin']); ?></span>
                    <a href="../../../controllers/admin/logout.php" class="logout-button">Cerrar Sesión</a>
                </div>
            </div>
        </header>

        <nav class="main-nav">
            <ul>
                <li><a href="admin_pacientes.php">Pacientes</a></li>
                <li><a href="admin_solicitudes.php">Solicitudes</a></li>
                <li><a href="admin_actividades.php" class="active">Actividades</a></li>
                </ul>
        </nav>

        <main>
            <div class="form-wrapper">
                <div class="form-header">
                    <h2>Asignar Nueva Actividad</h2>
                    <a href="admin_actividades.php" class="back-button">Volver al Listado</a>
                </div>
                
                <form action="tu_controlador_de_actividades.php" method="POST" class="styled-form">
                    <div class="form-group">
                        <label for="paciente_id">Asignar a Paciente:</label>
                        <select id="paciente_id" name="paciente_id" required>
                            <option value="">-- Seleccione un Paciente --</option>
                            <option value="1">Ana Torres</option>
                            <option value="2">Luis Vega</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="cuidador_id">Asignar a Cuidador:</label>
                        <select id="cuidador_id" name="cuidador_id" required>
                            <option value="">-- Seleccione un Cuidador --</option>
                            <option value="1">Carlos Ruiz</option>
                            <option value="2">Marta Gómez</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="descripcion">Descripción de la Actividad:</label>
                        <textarea id="descripcion" name="descripcion" rows="4" required placeholder="Ej: Tomar la presión arterial y registrarla."></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="fecha_hora">Fecha y Hora de la Actividad:</label>
                        <input type="datetime-local" id="fecha_hora" name="fecha_hora" required>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="submit-btn">Guardar Actividad</button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html> 
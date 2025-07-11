<?php
// 'verificar_sesion.php' protege la página, asegurando que solo usuarios autorizados entren.
require_once __DIR__ . '/../../../controllers/auth/verificar_sesion.php';
// 'actividad.php' y 'pacientes.php' son los modelos que nos conectan con la base de datos.
require_once __DIR__ . '/../../../models/clases/actividad.php';
require_once __DIR__ . '/../../../models/clases/pacientes.php';

// Confirma que el rol del usuario en sesión sea 'Administrador'. Si no, detiene la ejecución.
verificarAcceso(['Administrador']);

// Crea una instancia de los modelos para poder usar sus funciones (ej: consultar, obtenerPorId).
$modelo_actividad = new Actividad();
$modelo_paciente = new Paciente();

// Se define una variable para saber si el formulario se usará para editar o para crear.
// Por defecto, se asume que es para crear (false).
$modo_edicion = false;

// Prepara un array vacío que contendrá los datos de la actividad si estamos en modo de edición.
$actividad = [];

// Consulta y guarda la lista de todos los pacientes activos.
// Esto es necesario para llenar el menú desplegable de selección de paciente.
$pacientes = $modelo_paciente->consultar(); 

// Revisa si en la URL se pasó un 'id' (ej: ?id=123). Esto indica el modo de edición.
if (isset($_GET['id'])) {
    // Si se encuentra un 'id', activamos el modo de edición.
    $modo_edicion = true;
    // Se utiliza el ID para buscar los datos específicos de esa actividad y se guardan en el array.
    $actividad = $modelo_actividad->obtenerPorId($_GET['id']);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= $modo_edicion ? 'Editar' : 'Agregar' ?> Actividad</title>
    <link rel="stylesheet" href="../css_admin/historia_clinica_form.css"> 
</head>
<body>
    <div class="form-container">
        <h1><i class="fas fa-calendar-plus"></i> <?= $modo_edicion ? 'Editar Actividad' : 'Nueva Actividad' ?></h1>
        
        <form action="../../../controllers/admin/actividad/actividad_controller.php" method="POST">
            
            <input type="hidden" name="accion" value="<?= $modo_edicion ? 'actualizar' : 'registrar' ?>">
            
            <?php if ($modo_edicion) echo "<input type='hidden' name='id_actividad' value='{$actividad['id_actividad']}'>"; ?>

            <div class="form-grid">
                <div class="form-group full-width">
                    <label for="id_paciente">Paciente</label>
                    <select name="id_paciente" required>
                        <option value="">-- Seleccione un paciente --</option>
                        <?php 
                        // Itera sobre la lista de pacientes para crear cada opción del menú.
                        foreach ($pacientes as $paciente): 
                        ?>
                            <option value="<?= $paciente['id_paciente'] ?>" 
                                /** Lógica de preselección:
                                 * Si estamos en modo edición ($modo_edicion es true) Y el ID del paciente en esta
                                 * iteración es el mismo que el ID del paciente guardado en la actividad que estamos editando,
                                 * entonces se añade el atributo 'selected' a esta opción.
                                 */
                                <?= ($modo_edicion && $actividad['id_paciente'] == $paciente['id_paciente']) ? 'selected' : '' ?>>
                                
                                <?= htmlspecialchars($paciente['nombre'] . ' ' . $paciente['apellido']) ?>
                            </option>
                        <?php endforeach; // Finaliza el bucle. ?>
                    </select>
                </div>

                <div class="form-group"><label>Tipo de Actividad</label><input type="text" name="tipo_actividad" value="<?= htmlspecialchars($actividad['tipo_actividad'] ?? '') ?>" required></div>
                <div class="form-group"><label>Fecha</label><input type="date" name="fecha_actividad" value="<?= htmlspecialchars($actividad['fecha_actividad'] ?? '') ?>" required></div>
                <div class="form-group"><label>Hora Inicio</label><input type="time" name="hora_inicio" value="<?= htmlspecialchars($actividad['hora_inicio'] ?? '') ?>"></div>
                <div class="form-group"><label>Hora Fin</label><input type="time" name="hora_fin" value="<?= htmlspecialchars($actividad['hora_fin'] ?? '') ?>"></div>
                
                <div class="form-group full-width"><label>Descripción</label><textarea name="descripcion_actividad" rows="4"><?= htmlspecialchars($actividad['descripcion_actividad'] ?? '') ?></textarea></div>
            </div>

            <div class="form-actions">
                <a href="admin_actividades.php" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar</button>
            </div>
        </form>
    </div>
</body>
</html>
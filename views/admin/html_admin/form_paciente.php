<?php
// Suponemos que tienes una variable que indica si estás en modo de edición o no.
// Por ejemplo, podrías pasar un ID de paciente por GET.
$is_editing = isset($_GET['id_paciente']);
$paciente_data = null;

if ($is_editing) {
    // Aquí cargarías los datos del paciente desde la base de datos
    // $paciente_data = $pacienteModel->getPacienteById($_GET['id_paciente']);
}
?>

<div class="form-container">
    <h1><?php echo $is_editing ? 'Editar Paciente' : 'Agregar Nuevo Paciente'; ?></h1>
    <form id="form-paciente">
        <?php if ($is_editing && $paciente_data): ?>
            <input type="hidden" name="id_paciente" id="id_paciente" value="<?php echo htmlspecialchars($paciente_data['id_paciente']); ?>">
        <?php endif; ?>

        <label for="cedula">Cédula</label>
        <input type="text" id="cedula" name="cedula" value="<?php echo htmlspecialchars($paciente_data['cedula'] ?? ''); ?>" <?php echo $is_editing ? 'readonly' : ''; ?> required>

        <label for="nombre">Nombre</label>
        <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($paciente_data['nombre'] ?? ''); ?>" <?php echo $is_editing ? 'readonly' : ''; ?> required>

        <label for="apellido">Apellido</label>
        <input type="text" id="apellido" name="apellido" value="<?php echo htmlspecialchars($paciente_data['apellido'] ?? ''); ?>" <?php echo $is_editing ? 'readonly' : ''; ?> required>

        <label for="fecha_nacimiento">Fecha de Nacimiento</label>
        <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" value="<?php echo htmlspecialchars($paciente_data['fecha_nacimiento'] ?? ''); ?>" <?php echo $is_editing ? 'readonly' : ''; ?> required>

        <label for="tipo_sangre">Tipo de Sangre</label>
        <select id="tipo_sangre" name="tipo_sangre" <?php echo $is_editing ? 'disabled' : ''; ?> required>
            <option value="">Seleccione...</option>
            <option value="A+" <?php echo ($paciente_data['tipo_sangre'] ?? '') === 'A+' ? 'selected' : ''; ?>>A+</option>
            <option value="A-" <?php echo ($paciente_data['tipo_sangre'] ?? '') === 'A-' ? 'selected' : ''; ?>>A-</option>
            <option value="B+" <?php echo ($paciente_data['tipo_sangre'] ?? '') === 'B+' ? 'selected' : ''; ?>>B+</option>
            <option value="B-" <?php echo ($paciente_data['tipo_sangre'] ?? '') === 'B-' ? 'selected' : ''; ?>>B-</option>
            <option value="AB+" <?php echo ($paciente_data['tipo_sangre'] ?? '') === 'AB+' ? 'selected' : ''; ?>>AB+</option>
            <option value="AB-" <?php echo ($paciente_data['tipo_sangre'] ?? '') === 'AB-' ? 'selected' : ''; ?>>AB-</option>
            <option value="O+" <?php echo ($paciente_data['tipo_sangre'] ?? '') === 'O+' ? 'selected' : ''; ?>>O+</option>
            <option value="O-" <?php echo ($paciente_data['tipo_sangre'] ?? '') === 'O-' ? 'selected' : ''; ?>>O-</option>
        </select>

        <label for="telefono">Teléfono</label>
        <input type="text" id="telefono" name="telefono" value="<?php echo htmlspecialchars($paciente_data['telefono'] ?? ''); ?>" required>
        
        <label for="direccion">Dirección</label>
        <input type="text" id="direccion" name="direccion" value="<?php echo htmlspecialchars($paciente_data['direccion'] ?? ''); ?>" required>

        <label for="sexo">Sexo</label>
        <select id="sexo" name="sexo" required>
            <option value="">Seleccione...</option>
            <option value="Masculino" <?php echo ($paciente_data['sexo'] ?? '') === 'Masculino' ? 'selected' : ''; ?>>Masculino</option>
            <option value="Femenino" <?php echo ($paciente_data['sexo'] ?? '') === 'Femenino' ? 'selected' : ''; ?>>Femenino</option>
        </select>

        <button type="submit"><?php echo $is_editing ? 'Actualizar Paciente' : 'Agregar Paciente'; ?></button>
    </form>
</div>
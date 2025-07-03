<?php
session_start();
if (!isset($_SESSION['id_usuario']) || $_SESSION['nombre_rol'] !== 'Administrador') {
    header("Location: /GericareConnect/views/index-login/htmls/index.html");
    exit();
}
require_once __DIR__ . '/../../../models/clases/pacientes.php';
require_once __DIR__ . '/../../../models/clases/usuario.php'; // Incluimos la clase de usuario para buscar familiares

$modo_edicion = false;
$datos_paciente = [];

// Obtener la lista de familiares para el dropdown
$user_model = new usuario();
$lista_familiares = $user_model->obtenerUsuariosPorRol('Familiar'); // Asumiendo que tienes un método así

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $modo_edicion = true;
    $pacienteModel = new Paciente();
    $datos_paciente = $pacienteModel->obtenerPorId($_GET['id']);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= $modo_edicion ? 'Editar' : 'Agregar' ?> Paciente</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #28a745; --primary-hover: #218838; --light-gray: #f4f7f6;
            --medium-gray: #e9ecef; --dark-gray: #6c757d; --text-color: #343a40;
            --white: #ffffff; --shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }
        body { font-family: 'Poppins', sans-serif; background-color: var(--light-gray); color: var(--text-color); margin: 0; }
        .container { max-width: 900px; margin: 2rem auto; background: var(--white); padding: 2.5rem; border-radius: 12px; box-shadow: var(--shadow); }
        h1 { font-size: 1.8rem; font-weight: 600; margin-bottom: 2rem; display: flex; align-items: center; gap: 10px; color: var(--text-color); }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem 2rem; }
        .form-group { display: flex; flex-direction: column; }
        .form-group label { margin-bottom: 8px; font-weight: 500; color: var(--dark-gray); font-size: 0.9rem; }
        .form-group label i { margin-right: 8px; color: var(--primary-color); }
        input[type="text"], input[type="number"], input[type="date"], select, textarea {
            width: 100%; padding: 12px 15px; border: 1px solid var(--medium-gray); border-radius: 8px;
            font-family: 'Poppins', sans-serif; font-size: 1rem; transition: all 0.3s ease;
        }
        input:focus, select:focus, textarea:focus { outline: none; border-color: var(--primary-color); box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.2); }
        textarea { resize: vertical; min-height: 80px; }
        .form-group.full-width { grid-column: 1 / -1; }
        .toolbar { margin-top: 2rem; display: flex; justify-content: flex-end; gap: 1rem; }
        .btn { padding: 12px 25px; border: none; border-radius: 8px; font-size: 1rem; font-weight: 600; cursor: pointer; transition: all 0.3s ease; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; }
        .btn-primary { background: linear-gradient(45deg, var(--primary-color), #24c251); color: var(--white); }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(40, 167, 69, 0.3); }
        .btn-secondary { background-color: #6c757d; color: var(--white); }
        .btn-secondary:hover { background-color: #5a6268; }
        @media (max-width: 768px) { .form-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-<?= $modo_edicion ? 'user-edit' : 'user-plus' ?>"></i> <?= $modo_edicion ? 'Editar Paciente' : 'Registrar Paciente' ?></h1>
        <form action="../../../controllers/admin/paciente_controller.php" method="POST">
            <input type="hidden" name="accion" value="<?= $modo_edicion ? 'actualizar' : 'registrar' ?>">
            <?php if ($modo_edicion) echo "<input type='hidden' name='id_paciente' value='{$datos_paciente['id_paciente']}'>"; ?>
            
            <div class="form-grid">
                <div class="form-group"><label for="nombre"><i class="fas fa-user"></i> Nombres</label><input type="text" name="nombre" value="<?= htmlspecialchars($datos_paciente['nombre'] ?? '') ?>" required></div>
                <div class="form-group"><label for="apellido"><i class="fas fa-user-friends"></i> Apellidos</label><input type="text" name="apellido" value="<?= htmlspecialchars($datos_paciente['apellido'] ?? '') ?>" required></div>
                <div class="form-group"><label for="documento_identificacion"><i class="fas fa-id-card"></i> Documento</label><input type="number" name="documento_identificacion" value="<?= htmlspecialchars($datos_paciente['documento_identificacion'] ?? '') ?>" required></div>
                <div class="form-group"><label for="fecha_nacimiento"><i class="fas fa-calendar-alt"></i> Fecha de Nacimiento</label><input type="date" name="fecha_nacimiento" value="<?= htmlspecialchars($datos_paciente['fecha_nacimiento'] ?? '') ?>" required></div>
                <div class="form-group"><label for="genero"><i class="fas fa-venus-mars"></i> Género</label><select name="genero" required><option value="">Seleccione...</option><option value="Masculino" <?= (($datos_paciente['genero'] ?? '') == 'Masculino') ? 'selected' : '' ?>>Masculino</option><option value="Femenino" <?= (($datos_paciente['genero'] ?? '') == 'Femenino') ? 'selected' : '' ?>>Femenino</option></select></div>
                <div class="form-group"><label for="contacto_emergencia"><i class="fas fa-phone-alt"></i> Contacto de Emergencia</label><input type="text" name="contacto_emergencia" value="<?= htmlspecialchars($datos_paciente['contacto_emergencia'] ?? '') ?>" required></div>
                <div class="form-group"><label for="estado_civil"><i class="fas fa-ring"></i> Estado Civil</label><input type="text" name="estado_civil" value="<?= htmlspecialchars($datos_paciente['estado_civil'] ?? '') ?>" required></div>
                <div class="form-group"><label for="tipo_sangre"><i class="fas fa-tint"></i> Tipo de Sangre</label><select name="tipo_sangre" required><option value="">Seleccione...</option><option value="A+" <?= (($datos_paciente['tipo_sangre'] ?? '') == 'A+') ? 'selected' : '' ?>>A+</option><option value="A-" <?= (($datos_paciente['tipo_sangre'] ?? '') == 'A-') ? 'selected' : '' ?>>A-</option><option value="B+" <?= (($datos_paciente['tipo_sangre'] ?? '') == 'B+') ? 'selected' : '' ?>>B+</option><option value="B-" <?= (($datos_paciente['tipo_sangre'] ?? '') == 'B-') ? 'selected' : '' ?>>B-</option><option value="AB+" <?= (($datos_paciente['tipo_sangre'] ?? '') == 'AB+') ? 'selected' : '' ?>>AB+</option><option value="AB-" <?= (($datos_paciente['tipo_sangre'] ?? '') == 'AB-') ? 'selected' : '' ?>>AB-</option><option value="O+" <?= (($datos_paciente['tipo_sangre'] ?? '') == 'O+') ? 'selected' : '' ?>>O+</option><option value="O-" <?= (($datos_paciente['tipo_sangre'] ?? '') == 'O-') ? 'selected' : '' ?>>O-</option></select></div>
                <div class="form-group"><label for="seguro_medico"><i class="fas fa-file-medical"></i> Seguro Médico (EPS)</label><input type="text" name="seguro_medico" value="<?= htmlspecialchars($datos_paciente['seguro_medico'] ?? '') ?>"></div>
                <div class="form-group"><label for="numero_seguro"><i class="fas fa-hashtag"></i> Número de Afiliación</label><input type="text" name="numero_seguro" value="<?= htmlspecialchars($datos_paciente['numero_seguro'] ?? '') ?>"></div>

                <div class="form-group">
                    <label for="id_usuario_familiar"><i class="fas fa-link"></i> Familiar Asociado (Opcional)</label>
                    <select name="id_usuario_familiar" id="id_usuario_familiar">
                        <option value="">Ninguno</option>
                        <?php foreach ($lista_familiares as $familiar): ?>
                            <option value="<?= $familiar['id_usuario'] ?>" <?= (isset($datos_paciente['id_usuario_familiar']) && $datos_paciente['id_usuario_familiar'] == $familiar['id_usuario']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($familiar['nombre'] . ' ' . $familiar['apellido']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group full-width"><label for="alergias"><i class="fas fa-allergies"></i> Alergias Conocidas</label><textarea name="alergias"><?= htmlspecialchars($datos_paciente['alergias'] ?? '') ?></textarea></div>
            </div>
            <div class="toolbar">
                <a href="admin_pacientes.php" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> <?= $modo_edicion ? 'Actualizar Cambios' : 'Guardar Paciente' ?></button>
            </div>
        </form>
    </div>
</body>
</html>
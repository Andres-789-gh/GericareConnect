<?php
session_start();
// Seguridad: solo los administradores pueden acceder.
if (!isset($_SESSION['id_usuario']) || $_SESSION['nombre_rol'] !== 'Administrador') {
    header("Location: /GericareConnect/views/index-login/htmls/index.html");
    exit();
}

// Incluimos las clases para obtener datos de pacientes y, muy importante, de los usuarios (familiares).
require_once __DIR__ . '/../../../models/clases/pacientes.php';
require_once __DIR__ . '/../../../models/clases/usuario.php';

$modo_edicion = false;
$datos_paciente = [];

// ¡Aquí está la magia! Creamos un objeto usuario para usar la función que nos trae a los familiares.
$user_model = new usuario();
$lista_familiares = $user_model->obtenerUsuariosPorRol('Familiar');

// Si se recibe un ID en la URL, se activa el modo de edición.
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
            --primary-color: #007bff; --primary-hover: #0056b3; --light-gray: #f8f9fa;
            --medium-gray: #dee2e6; --dark-gray: #6c757d; --text-color: #212529;
            --white: #ffffff; --shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            --success-color: #28a745;
        }
        body { font-family: 'Poppins', sans-serif; background-color: var(--light-gray); color: var(--text-color); margin: 0; padding: 2rem; }
        .container { max-width: 950px; margin: 0 auto; background: var(--white); padding: 2rem 3rem; border-radius: 12px; box-shadow: var(--shadow); border-top: 5px solid var(--primary-color); }
        h1 { font-size: 1.8rem; font-weight: 600; margin-bottom: 2rem; display: flex; align-items: center; gap: 12px; color: var(--primary-color); }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem 2rem; }
        .form-group { display: flex; flex-direction: column; }
        .form-group label { margin-bottom: 8px; font-weight: 500; color: var(--dark-gray); font-size: 0.9rem; }
        .form-group label i { margin-right: 8px; color: var(--primary-color); }
        input, select, textarea {
            width: 100%; padding: 12px 15px; border: 1px solid var(--medium-gray); border-radius: 8px;
            font-family: 'Poppins', sans-serif; font-size: 1rem; transition: all 0.3s ease; box-sizing: border-box;
        }
        input:focus, select:focus, textarea:focus { outline: none; border-color: var(--primary-color); box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.2); }
        textarea { resize: vertical; min-height: 80px; }
        .form-group.full-width { grid-column: 1 / -1; }
        .toolbar { margin-top: 2rem; display: flex; justify-content: flex-end; gap: 1rem; }
        .btn { padding: 12px 25px; border: none; border-radius: 8px; font-size: 1rem; font-weight: 600; cursor: pointer; transition: all 0.3s ease; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; }
        .btn-primary { background: var(--success-color); color: var(--white); }
        .btn-primary:hover { background: #218838; transform: translateY(-2px); }
        .btn-secondary { background-color: var(--dark-gray); color: var(--white); }
        .btn-secondary:hover { background-color: #5a6268; }
        @media (max-width: 768px) { .form-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-user-plus"></i> <?= $modo_edicion ? 'Editar Paciente' : 'Registrar Paciente' ?></h1>
        <form action="../../../controllers/cuidador/paciente/paciente_controller.php" method="POST">
            <input type="hidden" name="accion" value="<?= $modo_edicion ? 'actualizar' : 'registrar' ?>">
            <?php if ($modo_edicion) echo "<input type='hidden' name='id_paciente' value='{$datos_paciente['id_paciente']}'>"; ?>
            
            <div class="form-grid">
                <div class="form-group"><label><i class="fas fa-user"></i> Nombres</label><input type="text" name="nombre" value="<?= htmlspecialchars($datos_paciente['nombre'] ?? '') ?>" required></div>
                <div class="form-group"><label><i class="fas fa-user-friends"></i> Apellidos</label><input type="text" name="apellido" value="<?= htmlspecialchars($datos_paciente['apellido'] ?? '') ?>" required></div>
                
                <div class="form-group">
                    <label for="id_usuario_familiar"><i class="fas fa-link"></i> Enlazar Familiar (Opcional)</label>
                    <select name="id_usuario_familiar" id="id_usuario_familiar">
                        <option value="">Ninguno</option> <?php foreach ($lista_familiares as $familiar): ?>
                            <option value="<?= $familiar['id_usuario'] ?>" <?= (isset($datos_paciente['id_usuario_familiar']) && $datos_paciente['id_usuario_familiar'] == $familiar['id_usuario']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($familiar['nombre'] . ' ' . $familiar['apellido']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="toolbar">
                <a href="admin_pacientes.php" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> <?= $modo_edicion ? 'Actualizar' : 'Guardar' ?></button>
            </div>
        </form>
    </div>
</body>
</html>
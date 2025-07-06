<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../../controllers/auth/verificar_sesion.php';
verificarAcceso(['Administrador']);
require_once __DIR__ . "/../../../controllers/admin/HC/enfermedad.controlador.php";

// Se crea una instancia del controlador para usarla en toda la página
$controlador = new ControladorEnfermedades();

// Lógica para procesar formularios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id_enfermedad_editar']) && !empty($_POST['id_enfermedad_editar'])) {
        $controlador->ctrEditarEnfermedad();
    } else {
        $controlador->ctrCrearEnfermedad();
    }
}
if (isset($_GET['idEliminarEnfermedad'])) {
    $controlador->ctrEliminarEnfermedad();
}

$return_url = $_GET['return_url'] ?? 'historia_clinica.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Enfermedades</title>
    <link rel="stylesheet" href="../../css/styles.css">
</head>
<body>
    <div class="container">
        <div style="margin-bottom: 20px;">
            <a href="<?= htmlspecialchars(urldecode($return_url)) ?>" class="btn btn-secondary">← Volver al Formulario</a>
        </div>
        
        <h1>Gestión de Enfermedades</h1>

        <div class="form-container">
            <form method="post" id="enfermedad-form" action="enfermedad.php?return_url=<?= urlencode($return_url) ?>">
                <h2 id="form-title">Agregar Nueva Enfermedad</h2>
                <input type="hidden" id="id_enfermedad_editar" name="id_enfermedad_editar">
                <div class="form-group">
                    <label for="nombre_enfermedad">Nombre:</label>
                    <input type="text" id="nombre_enfermedad" name="nombre_enfermedad" required>
                </div>
                <div class="form-group">
                    <label for="descripcion_enfermedad">Descripción:</label>
                    <textarea id="descripcion_enfermedad" name="descripcion_enfermedad"></textarea>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                    <button type="button" class="btn btn-secondary" id="btn-cancelar" style="display: none;">Cancelar Edición</button>
                </div>
            </form>
        </div>

        <div class="table-container">
            <h2>Listado de Enfermedades</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Se llama al método desde la instancia creada al inicio
                    $enfermedades = $controlador->ctrMostrarEnfermedades(null, null);
                    if (is_array($enfermedades)):
                        foreach ($enfermedades as $enfermedad):
                    ?>
                        <tr id="row-enfermedad-<?= $enfermedad['id_enfermedad'] ?>">
                            <td><?= htmlspecialchars($enfermedad["id_enfermedad"]); ?></td>
                            <td><?= htmlspecialchars($enfermedad["nombre_enfermedad"]); ?></td>
                            <td><?= htmlspecialchars($enfermedad["descripcion_enfermedad"]); ?></td>
                            <td>
                                <button class="btn btn-warning btn-editar" data-id="<?= $enfermedad["id_enfermedad"]; ?>" data-nombre="<?= htmlspecialchars($enfermedad["nombre_enfermedad"]); ?>" data-descripcion="<?= htmlspecialchars($enfermedad["descripcion_enfermedad"]); ?>">Editar</button>
                                <a href="enfermedad.php?idEliminarEnfermedad=<?= $enfermedad["id_enfermedad"]; ?>&return_url=<?= urlencode($return_url) ?>" class="btn btn-danger" onclick="return confirm('¿Estás seguro?');">Eliminar</a>
                                <button type="button" class="btn btn-success btn-seleccionar" 
                                        onclick="toggleSeleccion(this, <?= $enfermedad['id_enfermedad']; ?>, '<?= htmlspecialchars(addslashes($enfermedad['nombre_enfermedad'])); ?>', 'enfermedades')">
                                    Seleccionar
                                </button>
                            </td>
                        </tr>
                    <?php
                        endforeach;
                    endif;
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <script>
        function toggleSeleccion(button, id, nombre, tipo) {
            const key = `selected_${tipo}`;
            let seleccionados = JSON.parse(localStorage.getItem(key)) || [];
            const itemIndex = seleccionados.findIndex(item => item.id == id);

            if (itemIndex > -1) {
                seleccionados.splice(itemIndex, 1);
                button.textContent = 'Seleccionar';
                button.classList.remove('btn-danger');
                button.classList.add('btn-success');
            } else {
                seleccionados.push({ id: id, nombre: nombre });
                button.textContent = 'Quitar';
                button.classList.remove('btn-success');
                button.classList.add('btn-danger');
            }
            localStorage.setItem(key, JSON.stringify(seleccionados));
        }

        document.addEventListener('DOMContentLoaded', () => {
            const seleccionados = JSON.parse(localStorage.getItem('selected_enfermedades')) || [];
            seleccionados.forEach(item => {
                const row = document.getElementById(`row-enfermedad-${item.id}`);
                if (row) {
                    const button = row.querySelector('.btn-seleccionar');
                    button.textContent = 'Quitar';
                    button.classList.remove('btn-success');
                    button.classList.add('btn-danger');
                }
            });

            document.querySelectorAll('.btn-editar').forEach(button => {
                button.addEventListener('click', function() {
                    document.getElementById('form-title').textContent = 'Editar Enfermedad';
                    document.getElementById('id_enfermedad_editar').value = this.dataset.id;
                    document.getElementById('nombre_enfermedad').value = this.dataset.nombre;
                    document.getElementById('descripcion_enfermedad').value = this.dataset.descripcion;
                    document.getElementById('btn-cancelar').style.display = 'inline-block';
                    window.scrollTo(0, 0);
                });
            });
            document.getElementById('btn-cancelar').addEventListener('click', () => {
                document.getElementById('form-title').textContent = 'Agregar Nueva Enfermedad';
                document.getElementById('enfermedad-form').reset();
                document.getElementById('id_enfermedad_editar').value = '';
                document.getElementById('btn-cancelar').style.display = 'none';
            });
        });
    </script>
</body>
</html>
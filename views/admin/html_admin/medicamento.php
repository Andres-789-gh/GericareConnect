<?php
// views/admin/html_admin/medicamento.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Mantener la seguridad y carga de controladores
require_once __DIR__ . '/../../../controllers/auth/verificar_sesion.php';
verificarAcceso(['Administrador']);
require_once __DIR__ . "/../../../controllers/admin/HC/medicamento.controlador.php";

// Lógica para procesar formularios (sin cambios)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id_medicamento_editar']) && !empty($_POST['id_medicamento_editar'])) {
        ControladorMedicamentos::ctrEditarMedicamento();
    } else {
        ControladorMedicamentos::ctrCrearMedicamento();
    }
}
if (isset($_GET['idEliminar'])) {
    $controlador = new ControladorMedicamentos();
    $controlador->ctrEliminarMedicamento();
}

// Obtener la URL de retorno para el botón "Volver"
$return_url = $_GET['return_url'] ?? 'historia_clinica.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Medicamentos</title>
    <link rel="stylesheet" href="../../css/styles.css">
</head>
<body>
    <div class="container">
        <div style="margin-bottom: 20px;">
            <a href="<?= htmlspecialchars(urldecode($return_url)) ?>" class="btn btn-secondary">← Volver al Formulario</a>
        </div>
        
        <h1>Gestión de Medicamentos</h1>

        <div class="form-container">
            <form method="post" id="medicamento-form" action="medicamento.php?return_url=<?= urlencode($return_url) ?>">
                <h2 id="form-title">Agregar Nuevo Medicamento</h2>
                <input type="hidden" id="id_medicamento_editar" name="id_medicamento_editar">
                <div class="form-group">
                    <label for="nombre_medicamento">Nombre:</label>
                    <input type="text" id="nombre_medicamento" name="nombre_medicamento" required>
                </div>
                <div class="form-group">
                    <label for="descripcion_medicamento">Descripción:</label>
                    <textarea id="descripcion_medicamento" name="descripcion_medicamento"></textarea>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                    <button type="button" class="btn btn-secondary" id="btn-cancelar" style="display: none;">Cancelar Edición</button>
                </div>
            </form>
        </div>

        <div class="table-container">
            <h2>Listado de Medicamentos</h2>
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
                    $medicamentos = ControladorMedicamentos::ctrMostrarMedicamentos(null, null);
                    if (is_array($medicamentos)):
                        foreach ($medicamentos as $medicamento):
                    ?>
                        <tr id="row-medicamento-<?= $medicamento['id_medicamento'] ?>">
                            <td><?= htmlspecialchars($medicamento["id_medicamento"]); ?></td>
                            <td><?= htmlspecialchars($medicamento["nombre_medicamento"]); ?></td>
                            <td><?= htmlspecialchars($medicamento["descripcion_medicamento"]); ?></td>
                            <td>
                                <button class="btn btn-warning btn-editar" data-id="<?= $medicamento["id_medicamento"]; ?>" data-nombre="<?= htmlspecialchars($medicamento["nombre_medicamento"]); ?>" data-descripcion="<?= htmlspecialchars($medicamento["descripcion_medicamento"]); ?>">Editar</button>
                                <a href="medicamento.php?idEliminar=<?= $medicamento["id_medicamento"]; ?>&return_url=<?= urlencode($return_url) ?>" class="btn btn-danger" onclick="return confirm('¿Estás seguro?');">Eliminar</a>
                                <button type="button" class="btn btn-success btn-seleccionar" 
                                        onclick="toggleSeleccion(this, <?= $medicamento['id_medicamento']; ?>, '<?= htmlspecialchars(addslashes($medicamento['nombre_medicamento'])); ?>', 'medicamentos')">
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
            const seleccionados = JSON.parse(localStorage.getItem('selected_medicamentos')) || [];
            seleccionados.forEach(item => {
                const row = document.getElementById(`row-medicamento-${item.id}`);
                if (row) {
                    const button = row.querySelector('.btn-seleccionar');
                    button.textContent = 'Quitar';
                    button.classList.remove('btn-success');
                    button.classList.add('btn-danger');
                }
            });

            // Lógica para el formulario de edición (sin cambios)
            document.querySelectorAll('.btn-editar').forEach(button => {
                button.addEventListener('click', function() {
                    document.getElementById('form-title').textContent = 'Editar Medicamento';
                    document.getElementById('id_medicamento_editar').value = this.dataset.id;
                    document.getElementById('nombre_medicamento').value = this.dataset.nombre;
                    document.getElementById('descripcion_medicamento').value = this.dataset.descripcion;
                    document.getElementById('btn-cancelar').style.display = 'inline-block';
                    window.scrollTo(0, 0);
                });
            });
            document.getElementById('btn-cancelar').addEventListener('click', () => {
                document.getElementById('form-title').textContent = 'Agregar Nuevo Medicamento';
                document.getElementById('medicamento-form').reset();
                document.getElementById('id_medicamento_editar').value = '';
                document.getElementById('btn-cancelar').style.display = 'none';
            });
        });
    </script>
</body>
</html>
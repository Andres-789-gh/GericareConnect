<?php
require_once __DIR__ . "/../../../controllers/cuidador/medicamento.controlador.php";
require_once __DIR__ . "/../../../models/clases/medicamento.modelo.php"; 

$modoSeleccion = isset($_GET['seleccionar']) && $_GET['seleccionar'] === 'true';

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
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $modoSeleccion ? 'Seleccionar Medicamento' : 'Gestión de Medicamentos'; ?></title>
    <link rel="stylesheet" href="../../css/styles.css">
</head>
<body>
    <div class="container">
        <h1><?php echo $modoSeleccion ? 'Seleccione o Cree Medicamentos' : 'Gestión de Medicamentos'; ?></h1>

        <div class="form-container">
            <form method="post" id="medicamento-form" action="medicamento.php<?php echo $modoSeleccion ? '?seleccionar=true' : ''; ?>">
                <h2 id="form-title">Agregar Nuevo Medicamento</h2>
                <input type="hidden" id="id_medicamento_editar" name="id_medicamento_editar">
                <div class="form-group">
                    <label for="nombre_medicamento">Nombre del Medicamento:</label>
                    <input type="text" id="nombre_medicamento" name="nombre_medicamento" required>
                </div>
                <div class="form-group">
                    <label for="descripcion_medicamento">Descripción:</label>
                    <textarea id="descripcion_medicamento" name="descripcion_medicamento"></textarea>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                    <button type="button" class="btn btn-secondary" id="btn-cancelar" style="display: none;">Cancelar</button>
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
                    if (is_array($medicamentos) && count($medicamentos) > 0) {
                        foreach ($medicamentos as $medicamento):
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($medicamento["id_medicamento"]); ?></td>
                            <td><?php echo htmlspecialchars($medicamento["nombre_medicamento"]); ?></td>
                            <td><?php echo htmlspecialchars($medicamento["descripcion_medicamento"]); ?></td>
                            <td>
                                <button class="btn btn-warning btn-editar" data-id="<?php echo $medicamento["id_medicamento"]; ?>" data-nombre="<?php echo htmlspecialchars($medicamento["nombre_medicamento"]); ?>" data-descripcion="<?php echo htmlspecialchars($medicamento["descripcion_medicamento"]); ?>">Editar</button>
                                <a href="medicamento.php?idEliminar=<?php echo $medicamento["id_medicamento"]; ?><?php echo $modoSeleccion ? '&seleccionar=true' : ''; ?>" class="btn btn-danger" onclick="return confirm('¿Estás seguro?');">Eliminar</a>
                                
                                <?php if ($modoSeleccion): ?>
                                    <button type="button" class="btn btn-primary" onclick="seleccionarItem(<?php echo $medicamento['id_medicamento']; ?>, '<?php echo htmlspecialchars(addslashes($medicamento['nombre_medicamento'])); ?>', 'medicamentos')">Seleccionar</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php
                        endforeach;
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function seleccionarItem(id, nombre, tipo) {
            const key = `selected_${tipo}`;
            let seleccionados = JSON.parse(localStorage.getItem(key)) || [];
            if (!seleccionados.some(item => item.id == id)) {
                seleccionados.push({ id: id, nombre: nombre });
                localStorage.setItem(key, JSON.stringify(seleccionados));
            }
            window.close();
        }

        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('medicamento-form');
            const formTitle = document.getElementById('form-title');
            const idMedicamentoEditar = document.getElementById('id_medicamento_editar');
            const nombreMedicamento = document.getElementById('nombre_medicamento');
            const descripcionMedicamento = document.getElementById('descripcion_medicamento');
            const btnCancelar = document.getElementById('btn-cancelar');

            document.querySelectorAll('.btn-editar').forEach(button => {
                button.addEventListener('click', function() {
                    formTitle.textContent = 'Editar Medicamento';
                    idMedicamentoEditar.value = this.getAttribute('data-id');
                    nombreMedicamento.value = this.getAttribute('data-nombre');
                    descripcionMedicamento.value = this.getAttribute('data-descripcion');
                    btnCancelar.style.display = 'inline-block';
                    window.scrollTo(0, 0);
                });
            });

            btnCancelar.addEventListener('click', () => {
                formTitle.textContent = 'Agregar Nuevo Medicamento';
                idMedicamentoEditar.value = '';
                form.reset();
                btnCancelar.style.display = 'none';
            });
        });
    </script>
</body>
</html>
<?php
// Incluimos los archivos del controlador y modelo
// RUTA CORREGIDA: Desde 'views/cuidador/html_cuidador/' sube TRES niveles para llegar a la raíz (GericareConnect/),
// y luego entra a 'controllers/cuidador/'
require_once __DIR__ . "/../../../controllers/cuidador/medicamento.controlador.php";
require_once __DIR__ . "/../../../models/clases/medicamento.modelo.php"; 

// Procesar acciones de POST (Crear/Editar)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id_medicamento_editar']) && !empty($_POST['id_medicamento_editar'])) {
        ControladorMedicamentos::ctrEditarMedicamento();
    } else {
        ControladorMedicamentos::ctrCrearMedicamento();
    }
}

// Procesar acción de GET (Eliminar - ahora borrado lógico)
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
    <title>CRUD Medicamentos</title>
    <link rel="stylesheet" href="../../css/styles.css">
</head>
<body>
    <div class="container">
        <h1>Gestión de Medicamentos</h1>

        <div class="form-container">
            <form method="post" id="medicamento-form">
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
                                <button class="btn btn-warning btn-editar"
                                        data-id="<?php echo $medicamento["id_medicamento"]; ?>"
                                        data-nombre="<?php echo htmlspecialchars($medicamento["nombre_medicamento"]); ?>"
                                        data-descripcion="<?php echo htmlspecialchars($medicamento["descripcion_medicamento"]); ?>">
                                    Editar
                                </button>
                                <a href="medicamento.php?idEliminar=<?php echo $medicamento["id_medicamento"]; ?>"
                                   class="btn btn-danger"
                                   onclick="return confirm('¿Estás seguro de que quieres ELIMINAR este medicamento? (Se inhabilitará)');">
                                   Eliminar
                                </a>
                            </td>
                        </tr>
                        <?php
                        endforeach;
                    } else {
                        echo '<tr><td colspan="3">No hay medicamentos activos registrados.</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('medicamento-form');
            const formTitle = document.getElementById('form-title');
            const idMedicamentoEditar = document.getElementById('id_medicamento_editar');
            const nombreMedicamento = document.getElementById('nombre_medicamento');
            const descripcionMedicamento = document.getElementById('descripcion_medicamento');
            const btnCancelar = document.getElementById('btn-cancelar');

            document.querySelectorAll('.btn-editar').forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const nombre = this.getAttribute('data-nombre');
                    const descripcion = this.getAttribute('data-descripcion');

                    formTitle.textContent = 'Editar Medicamento';
                    idMedicamentoEditar.value = id;
                    nombreMedicamento.value = nombre;
                    descripcionMedicamento.value = descripcion;
                    btnCancelar.style.display = 'inline-block';
                });
            });

            btnCancelar.addEventListener('click', () => {
                formTitle.textContent = 'Agregar Nuevo Medicamento';
                idMedicamentoEditar.value = '';
                nombreMedicamento.value = '';
                descripcionMedicamento.value = '';
                btnCancelar.style.display = 'none';
            });
        });
    </script>
</body>
</html>
<?php
// Incluimos los archivos del modelo y controlador
require_once "../controllers/medicamento.controlador.php"; // Original path
require_once "../models/medicamento.modelo.php"; // Original path

// Procesar acciones de POST (Crear/Editar)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id_medicamento_editar']) && !empty($_POST['id_medicamento_editar'])) {
        // Es una edición
        ControladorMedicamentos::ctrEditarMedicamento();
    } else {
        // Es una creación
        ControladorMedicamentos::ctrCrearMedicamento();
    }
}

// Procesar acción de GET (Eliminar y Cambiar Estado)
if (isset($_GET['idEliminar'])) {
    $controlador = new ControladorMedicamentos(); // Instanciamos para llamar a métodos no estáticos
    $controlador->ctrEliminarMedicamento();
}

// Procesar cambio de estado
if (isset($_GET['idCambiarEstado']) && isset($_GET['nuevoEstado'])) {
    $controlador = new ControladorMedicamentos(); // Instanciamos para llamar a métodos no estáticos
    $controlador->ctrCambiarEstadoMedicamento();
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Medicamentos</title>
    <link rel="stylesheet" href="css/styles.css"> </head>
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
                <div class="form-group">
                    <label for="estado">Estado:</label>
                    <select id="estado" name="estado">
                        <option value="Activo">Activo</option>
                        <option value="Inactivo">Inactivo</option>
                    </select>
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
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Obtenemos todos los medicamentos para mostrarlos en la tabla
                    $medicamentos = ControladorMedicamentos::ctrMostrarMedicamentos(null, null);

                    // Check if $medicamentos is an array and not empty to avoid errors
                    if (is_array($medicamentos) && count($medicamentos) > 0) {
                        foreach ($medicamentos as $medicamento):
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($medicamento["id_medicamento"]); ?></td>
                            <td><?php echo htmlspecialchars($medicamento["nombre_medicamento"]); ?></td>
                            <td><?php echo htmlspecialchars($medicamento["descripcion_medicamento"]); ?></td>
                            <td><?php echo htmlspecialchars($medicamento["estado"]); ?></td>
                            <td>
                                <button class="btn btn-warning btn-editar"
                                        data-id="<?php echo $medicamento["id_medicamento"]; ?>"
                                        data-nombre="<?php echo htmlspecialchars($medicamento["nombre_medicamento"]); ?>"
                                        data-descripcion="<?php echo htmlspecialchars($medicamento["descripcion_medicamento"]); ?>"
                                        data-estado="<?php echo htmlspecialchars($medicamento["estado"]); ?>"> Editar
                                </button>
                                <a href="medicamento.php?idEliminar=<?php echo $medicamento["id_medicamento"]; ?>"
                                   class="btn btn-danger"
                                   onclick="return confirm('¿Estás seguro de que quieres eliminar este medicamento?');">
                                    Eliminar
                                </a>
                                <?php
                                // Logic for state change button
                                // IMPORTANT: These comparisons MUST exactly match your ENUM values in the database
                                if ($medicamento["estado"] == "Activo") { // Assuming 'Activo' with capital A in DB ENUM
                                    echo '<a href="medicamento.php?idCambiarEstado=' . $medicamento["id_medicamento"] . '&nuevoEstado=Inactivo"
                                           class="btn btn-info"
                                           onclick="return confirm(\'¿Estás seguro de que quieres INACTIVAR este medicamento?\');">
                                           Inactivar
                                          </a>';
                                } else { // Assuming 'Inactivo' with capital I in DB ENUM
                                    echo '<a href="medicamento.php?idCambiarEstado=' . $medicamento["id_medicamento"] . '&nuevoEstado=Activo"
                                           class="btn btn-success"
                                           onclick="return confirm(\'¿Estás seguro de que quieres ACTIVAR este medicamento?\');">
                                           Activar
                                          </a>';
                                }
                                ?>
                            </td>
                        </tr>
                        <?php
                        endforeach;
                    } else {
                        // Message if no medicines are registered
                        echo '<tr><td colspan="5">No hay medicamentos registrados.</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <script>
        // Script to handle the edit form and cancel button
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('medicamento-form');
            const formTitle = document.getElementById('form-title');
            const idMedicamentoEditar = document.getElementById('id_medicamento_editar');
            const nombreMedicamento = document.getElementById('nombre_medicamento');
            const descripcionMedicamento = document.getElementById('descripcion_medicamento');
            const estadoSelect = document.getElementById('estado'); // NEW: Reference to the state select
            const btnCancelar = document.getElementById('btn-cancelar');

            document.querySelectorAll('.btn-editar').forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const nombre = this.getAttribute('data-nombre');
                    const descripcion = this.getAttribute('data-descripcion');
                    const estado = this.getAttribute('data-estado'); // NEW: Get the state from data attribute

                    formTitle.textContent = 'Editar Medicamento';
                    idMedicamentoEditar.value = id;
                    nombreMedicamento.value = nombre;
                    descripcionMedicamento.value = descripcion;
                    estadoSelect.value = estado; // NEW: Set the value of the select dropdown
                    btnCancelar.style.display = 'inline-block'; // Show Cancel button
                });
            });

            btnCancelar.addEventListener('click', () => {
                formTitle.textContent = 'Agregar Nuevo Medicamento';
                idMedicamentoEditar.value = '';
                nombreMedicamento.value = '';
                descripcionMedicamento.value = '';
                estadoSelect.value = 'Activo'; // Reset the select to default (assuming 'Activo' is default)
                btnCancelar.style.display = 'none'; // Hide Cancel button
            });
        });
    </script>
</body>
</html>
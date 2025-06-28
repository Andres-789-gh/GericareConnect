<?php
// Incluimos los archivos del modelo y controlador
require_once "../controllers/enfermedad.controlador.php";
require_once "../models/enfermedad.modelo.php";

// Procesar acciones de POST (Crear/Editar)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id_enfermedad_editar']) && !empty($_POST['id_enfermedad_editar'])) {
        // Es una edición
        ControladorEnfermedades::ctrEditarEnfermedad();
    } else {
        // Es una creación
        ControladorEnfermedades::ctrCrearEnfermedad();
    }
}

// Procesar acción de GET (Eliminar y Cambiar Estado)
if (isset($_GET['idEliminarEnfermedad'])) {
    $controlador = new ControladorEnfermedades();
    $controlador->ctrEliminarEnfermedad();
}

// Procesar cambio de estado
if (isset($_GET['idCambiarEstadoEnfermedad']) && isset($_GET['nuevoEstadoEnfermedad'])) {
    $controlador = new ControladorEnfermedades();
    $controlador->ctrCambiarEstadoEnfermedad();
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Enfermedades</title>
    <link rel="stylesheet" href="css/styles.css"> <!-- Asegúrate de que esta ruta sea correcta para tus estilos -->
</head>
<body>
    <div class="container">
        <h1>Gestión de Enfermedades</h1>

        <div class="form-container">
            <form method="post" id="enfermedad-form">
                <h2 id="form-title">Agregar Nueva Enfermedad</h2>

                <input type="hidden" id="id_enfermedad_editar" name="id_enfermedad_editar">

                <div class="form-group">
                    <label for="nombre_enfermedad">Nombre de la Enfermedad:</label>
                    <input type="text" id="nombre_enfermedad" name="nombre_enfermedad" required>
                </div>
                <div class="form-group">
                    <label for="descripcion_enfermedad">Descripción:</label>
                    <textarea id="descripcion_enfermedad" name="descripcion_enfermedad"></textarea>
                </div>
                <div class="form-group">
                    <label for="estado">Estado:</label>
                    <select id="estado" name="estado">
                        <!-- Los valores deben coincidir exactamente con tu ENUM en la DB: 'Activo', 'Inactivo' -->
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
            <h2>Listado de Enfermedades</h2>
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
                    // Obtenemos todas las enfermedades para mostrarlas en la tabla
                    $enfermedades = ControladorEnfermedades::ctrMostrarEnfermedades(null, null);

                    // Verificamos si hay enfermedades para evitar errores si la tabla está vacía
                    if (is_array($enfermedades) && count($enfermedades) > 0) {
                        foreach ($enfermedades as $enfermedad):
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($enfermedad["id_enfermedad"]); ?></td>
                            <td><?php echo htmlspecialchars($enfermedad["nombre_enfermedad"]); ?></td>
                            <td><?php echo htmlspecialchars($enfermedad["descripcion_enfermedad"]); ?></td>
                            <td><?php echo htmlspecialchars($enfermedad["estado"]); ?></td>
                            <td>
                                <button class="btn btn-warning btn-editar"
                                        data-id="<?php echo $enfermedad["id_enfermedad"]; ?>"
                                        data-nombre="<?php echo htmlspecialchars($enfermedad["nombre_enfermedad"]); ?>"
                                        data-descripcion="<?php echo htmlspecialchars($enfermedad["descripcion_enfermedad"]); ?>"
                                        data-estado="<?php echo htmlspecialchars($enfermedad["estado"]); ?>">
                                    Editar
                                </button>
                                <a href="enfermedad.php?idEliminarEnfermedad=<?php echo $enfermedad["id_enfermedad"]; ?>"
                                   class="btn btn-danger"
                                   onclick="return confirm('¿Estás seguro de que quieres eliminar esta enfermedad?');">
                                    Eliminar
                                </a>
                                <?php
                                // Lógica para el botón de cambio de estado
                                // Los valores aquí deben coincidir exactamente con tu ENUM en la DB: 'Activo', 'Inactivo'
                                if ($enfermedad["estado"] == "Activo") {
                                    echo '<a href="enfermedad.php?idCambiarEstadoEnfermedad=' . $enfermedad["id_enfermedad"] . '&nuevoEstadoEnfermedad=Inactivo"
                                           class="btn btn-info"
                                           onclick="return confirm(\'¿Estás seguro de que quieres INACTIVAR esta enfermedad?\');">
                                           Inactivar
                                          </a>';
                                } else {
                                    echo '<a href="enfermedad.php?idCambiarEstadoEnfermedad=' . $enfermedad["id_enfermedad"] . '&nuevoEstadoEnfermedad=Activo"
                                           class="btn btn-success"
                                           onclick="return confirm(\'¿Estás seguro de que quieres ACTIVAR esta enfermedad?\');">
                                           Activar
                                          </a>';
                                }
                                ?>
                            </td>
                        </tr>
                        <?php
                        endforeach;
                    } else {
                        // Mensaje si no hay enfermedades
                        echo '<tr><td colspan="5">No hay enfermedades registradas.</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Script JavaScript para manejar la edición del formulario -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('enfermedad-form');
            const formTitle = document.getElementById('form-title');
            const idEnfermedadEditar = document.getElementById('id_enfermedad_editar');
            const nombreEnfermedad = document.getElementById('nombre_enfermedad');
            const descripcionEnfermedad = document.getElementById('descripcion_enfermedad');
            const estadoSelect = document.getElementById('estado');
            const btnCancelar = document.getElementById('btn-cancelar');

            document.querySelectorAll('.btn-editar').forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const nombre = this.getAttribute('data-nombre');
                    const descripcion = this.getAttribute('data-descripcion');
                    const estado = this.getAttribute('data-estado');

                    formTitle.textContent = 'Editar Enfermedad';
                    idEnfermedadEditar.value = id;
                    nombreEnfermedad.value = nombre;
                    descripcionEnfermedad.value = descripcion;
                    estadoSelect.value = estado; // Establecer el valor del select
                    btnCancelar.style.display = 'inline-block'; // Mostrar botón Cancelar
                });
            });

            btnCancelar.addEventListener('click', () => {
                formTitle.textContent = 'Agregar Nueva Enfermedad';
                idEnfermedadEditar.value = '';
                nombreEnfermedad.value = '';
                descripcionEnfermedad.value = '';
                estadoSelect.value = 'Activo'; // Restablecer el select al valor por defecto
                btnCancelar.style.display = 'none'; // Ocultar botón Cancelar
            });
        });
    </script>
</body>
</html>
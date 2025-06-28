<?php
// Incluimos los archivos del controlador y modelo
// RUTA CORREGIDA: Desde 'views/cuidador/html_cuidador/' sube TRES niveles para llegar a la raíz (GericareConnect/),
// y luego entra a 'controllers/cuidador/'
require_once "../../../controllers/cuidador/enfermedad.controlador.php";
// RUTA CORREGIDA: Desde 'views/cuidador/html_cuidador/' sube TRES niveles para llegar a la raíz (GericareConnect/),
// y luego entra a 'models/clases/'
require_once "../../../models/clases/enfermedad.modelo.php";

// Procesar acciones de POST (Crear/Editar)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id_enfermedad_editar']) && !empty($_POST['id_enfermedad_editar'])) {
        ControladorEnfermedades::ctrEditarEnfermedad();
    } else {
        ControladorEnfermedades::ctrCrearEnfermedad();
    }
}

// Procesar acción de GET (Eliminar - ahora borrado lógico)
if (isset($_GET['idEliminarEnfermedad'])) {
    $controlador = new ControladorEnfermedades();
    $controlador->ctrEliminarEnfermedad();
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Enfermedades</title>
    <link rel="stylesheet" href="../../../css/styles.css">
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
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $enfermedades = ControladorEnfermedades::ctrMostrarEnfermedades(null, null);

                    if (is_array($enfermedades) && count($enfermedades) > 0) {
                        foreach ($enfermedades as $enfermedad):
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($enfermedad["id_enfermedad"]); ?></td>
                            <td><?php echo htmlspecialchars($enfermedad["nombre_enfermedad"]); ?></td>
                            <td><?php echo htmlspecialchars($enfermedad["descripcion_enfermedad"]); ?></td>
                            <td>
                                <button class="btn btn-warning btn-editar"
                                        data-id="<?php echo $enfermedad["id_enfermedad"]; ?>"
                                        data-nombre="<?php echo htmlspecialchars($enfermedad["nombre_enfermedad"]); ?>"
                                        data-descripcion="<?php echo htmlspecialchars($enfermedad["descripcion_enfermedad"]); ?>">
                                    Editar
                                </button>
                                <a href="enfermedad.php?idEliminarEnfermedad=<?php echo $enfermedad["id_enfermedad"]; ?>"
                                   class="btn btn-danger"
                                   onclick="return confirm('¿Estás seguro de que quieres ELIMINAR esta enfermedad? (Se inhabilitará)');">
                                   Eliminar
                                </a>
                            </td>
                        </tr>
                        <?php
                        endforeach;
                    } else {
                        echo '<tr><td colspan="3">No hay enfermedades activas registradas.</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('enfermedad-form');
            const formTitle = document.getElementById('form-title');
            const idEnfermedadEditar = document.getElementById('id_enfermedad_editar');
            const nombreEnfermedad = document.getElementById('nombre_enfermedad');
            const descripcionEnfermedad = document.getElementById('descripcion_enfermedad');
            const btnCancelar = document.getElementById('btn-cancelar');

            document.querySelectorAll('.btn-editar').forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const nombre = this.getAttribute('data-nombre');
                    const descripcion = this.getAttribute('data-descripcion');

                    formTitle.textContent = 'Editar Enfermedad';
                    idEnfermedadEditar.value = id;
                    nombreEnfermedad.value = nombre;
                    descripcionEnfermedad.value = descripcion;
                    btnCancelar.style.display = 'inline-block';
                });
            });

            btnCancelar.addEventListener('click', () => {
                formTitle.textContent = 'Agregar Nueva Enfermedad';
                idEnfermedadEditar.value = '';
                nombreEnfermedad.value = '';
                descripcionEnfermedad.value = '';
                btnCancelar.style.display = 'none';
            });
        });
    </script>
</body>
</html>
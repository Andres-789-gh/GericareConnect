<?php
require_once __DIR__ . "/../../../controllers/cuidador/enfermedad.controlador.php";
require_once __DIR__ . "/../../../models/clases/enfermedad.modelo.php";

$modoSeleccion = isset($_GET['seleccionar']) && $_GET['seleccionar'] === 'true';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id_enfermedad_editar']) && !empty($_POST['id_enfermedad_editar'])) {
        ControladorEnfermedades::ctrEditarEnfermedad();
    } else {
        ControladorEnfermedades::ctrCrearEnfermedad();
    }
}
if (isset($_GET['idEliminarEnfermedad'])) {
    $controlador = new ControladorEnfermedades();
    $controlador->ctrEliminarEnfermedad();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo $modoSeleccion ? 'Seleccionar Enfermedad' : 'Gestión de Enfermedades'; ?></title>
    <link rel="stylesheet" href="../../css/styles.css">
</head>
<body>
    <div class="container">
        <h1><?php echo $modoSeleccion ? 'Seleccione o Cree Enfermedades' : 'Gestión de Enfermedades'; ?></h1>
        <div class="form-container">
            <form method="post" id="enfermedad-form" action="enfermedad.php<?php echo $modoSeleccion ? '?seleccionar=true' : ''; ?>">
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
                                <button class="btn btn-warning btn-editar" data-id="<?php echo $enfermedad["id_enfermedad"]; ?>" data-nombre="<?php echo htmlspecialchars($enfermedad["nombre_enfermedad"]); ?>" data-descripcion="<?php echo htmlspecialchars($enfermedad["descripcion_enfermedad"]); ?>">Editar</button>
                                <a href="enfermedad.php?idEliminarEnfermedad=<?php echo $enfermedad["id_enfermedad"]; ?><?php echo $modoSeleccion ? '&seleccionar=true' : ''; ?>" class="btn btn-danger" onclick="return confirm('¿Estás seguro?');">Eliminar</a>
                                
                                <?php if ($modoSeleccion): ?>
                                    <button type="button" class="btn btn-primary" onclick="seleccionarItem(<?php echo $enfermedad['id_enfermedad']; ?>, '<?php echo htmlspecialchars(addslashes($enfermedad['nombre_enfermedad'])); ?>', 'enfermedad')">Seleccionar</button>
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
            const key = `selected_${tipo}s`;
            let seleccionados = JSON.parse(localStorage.getItem(key)) || [];
            if (!seleccionados.some(item => item.id == id)) {
                seleccionados.push({ id: id, nombre: nombre });
                localStorage.setItem(key, JSON.stringify(seleccionados));
            }
            window.close();
        }

        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('enfermedad-form');
            const formTitle = document.getElementById('form-title');
            const idEnfermedadEditar = document.getElementById('id_enfermedad_editar');
            const nombreEnfermedad = document.getElementById('nombre_enfermedad');
            const descripcionEnfermedad = document.getElementById('descripcion_enfermedad');
            const btnCancelar = document.getElementById('btn-cancelar');

            document.querySelectorAll('.btn-editar').forEach(button => {
                button.addEventListener('click', function() {
                    formTitle.textContent = 'Editar Enfermedad';
                    idEnfermedadEditar.value = this.getAttribute('data-id');
                    nombreEnfermedad.value = this.getAttribute('data-nombre');
                    descripcionEnfermedad.value = this.getAttribute('data-descripcion');
                    btnCancelar.style.display = 'inline-block';
                    window.scrollTo(0, 0);
                });
            });

            btnCancelar.addEventListener('click', () => {
                formTitle.textContent = 'Agregar Nueva Enfermedad';
                form.reset();
                idEnfermedadEditar.value = '';
                btnCancelar.style.display = 'none';
            });
        });
    </script>
</body>
</html>
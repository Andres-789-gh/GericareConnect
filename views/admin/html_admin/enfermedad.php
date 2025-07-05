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
                                    <button type="button" class="btn btn-primary" onclick="seleccionarItem(<?php echo $enfermedad['id_enfermedad']; ?>, '<?php echo htmlspecialchars(addslashes($enfermedad['nombre_enfermedad'])); ?>', 'enfermedades')">Seleccionar</button>
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
            console.log(`[ENFERMEDAD - seleccionarItem] INICIA - Tipo: ${tipo}, ID: ${id}, Nombre: ${nombre}`); // Log 1

            const key = `selected_${tipo}`;
            let seleccionados = [];

            try {
                seleccionados = JSON.parse(localStorage.getItem(key)) || [];
                console.log(`[ENFERMEDAD - seleccionarItem] Contenido actual de localStorage (${key}):`, seleccionados); // Log 2
            } catch (e) {
                console.error(`[ENFERMEDAD - seleccionarItem] ERROR al parsear JSON de localStorage para ${key}:`, e); // Log 3
            }

            if (!seleccionados.some(item => item.id == id)) {
                seleccionados.push({ id: id, nombre: nombre });
                try {
                    localStorage.setItem(key, JSON.stringify(seleccionados));
                    console.log(`[ENFERMEDAD - seleccionarItem] Guardado exitoso en localStorage (${key}):`, seleccionados); // Log 4
                } catch (e) {
                    console.error(`[ENFERMEDAD - seleccionarItem] ERROR al guardar en localStorage para ${key}:`, e); // Log 5
                }
            } else {
                console.log(`[ENFERMEDAD - seleccionarItem] El item ya existe, no se añadió de nuevo.`); // Log 6
            }
            
            window.close();
            console.log(`[ENFERMEDAD - seleccionarItem] Ventana cerrada.`); // Log 7
        }
    </script>
</body>
</html>
<?php
// Se añade la verificación de sesión para mayor seguridad.
require_once __DIR__ . "/../../../controllers/auth/verificar_sesion.php";
verificarAcceso(['Administrador']);

require_once __DIR__ . "/../../../controllers/admin/HC/enfermedad.controlador.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id_enfermedad_editar']) && !empty($_POST['id_enfermedad_editar'])) {
        ControladorEnfermedadesAdmin::ctrEditarEnfermedad();
    } else {
        ControladorEnfermedadesAdmin::ctrCrearEnfermedad();
    }
}

if (isset($_GET['idEliminarEnfermedad'])) {
    $controlador = new ControladorEnfermedadesAdmin();
    $controlador->ctrEliminarEnfermedad();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Enfermedades</title>
    <link rel="stylesheet" href="../../css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .admin-header {
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem; /* Espacio para separar del contenido */
        }
        .logo-container { display: flex; align-items: center; }
        .logo { width: 40px; cursor: pointer; margin-right: 10px; }
        .app-name { font-size: 1.3rem; font-weight: 600; color: #343a40; }
        nav ul { list-style: none; margin: 0; padding: 0; }
        nav ul li a { text-decoration: none; color: #555; font-weight: 500; transition: color 0.3s; display: flex; align-items: center; gap: 8px;}
        nav ul li a:hover { color: #007bff; }
    </style>
</head>
<body>
    <header class="admin-header">
        <div class="logo-container">
            <img src="../../imagenes/Geri_Logo-..png" alt="Logo" class="logo" onclick="window.location.href='historia_clinica.php'">
            <span class="app-name">GERICARE CONNECT</span>
        </div>
        <nav>
            <ul>
                <li><a href="historia_clinica.php"><i class="fas fa-arrow-left"></i> Volver a Historias Clínicas</a></li>
            </ul>
        </nav>
    </header>
    <div class="container">
        <h1>Gestión de Enfermedades</h1>
        <div class="form-container">
            <form method="post" id="enfermedad-form">
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
                    $enfermedades = ControladorEnfermedadesAdmin::ctrMostrarEnfermedades(null, null);
                    if (is_array($enfermedades) && !empty($enfermedades)) {
                        foreach ($enfermedades as $enfermedad):
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($enfermedad["id_enfermedad"]) ?></td>
                            <td><?= htmlspecialchars($enfermedad["nombre_enfermedad"]) ?></td>
                            <td><?= htmlspecialchars($enfermedad["descripcion_enfermedad"]) ?></td>
                            <td>
                                <button class="btn btn-warning btn-editar"
                                        data-id="<?= $enfermedad["id_enfermedad"] ?>"
                                        data-nombre="<?= htmlspecialchars($enfermedad["nombre_enfermedad"]) ?>"
                                        data-descripcion="<?= htmlspecialchars($enfermedad["descripcion_enfermedad"]) ?>">
                                    Editar
                                </button>
                                <a href="gestion_enfermedades.php?idEliminarEnfermedad=<?= $enfermedad["id_enfermedad"] ?>"
                                   class="btn btn-danger" onclick="return confirm('¿Estás seguro de que quieres desactivar esta enfermedad?')">
                                   Eliminar
                                </a>
                            </td>
                        </tr>
                        <?php
                        endforeach;
                    } else {
                        echo '<tr><td colspan="4">No hay enfermedades registradas.</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
            <div class="export-button" style="margin-top: 20px; text-align: right;">
                <a href="../../../controllers/admin/HC/exportar_enfermedades.php" class="btn btn-success">
                    <i class="fas fa-file-excel"></i> Exportar a Excel
                </a>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
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
                document.getElementById('id_enfermedad_editar').value = '';
                document.getElementById('enfermedad-form').reset();
                document.getElementById('btn-cancelar').style.display = 'none';
            });
        });
    </script>
</body>
</html>
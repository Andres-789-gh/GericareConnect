<?php
// Se añade la verificación de sesión para mayor seguridad.
require_once __DIR__ . "/../../../controllers/auth/verificar_sesion.php";
verificarAcceso(['Administrador']);

require_once __DIR__ . "/../../../controllers/admin/HC/medicamento.controlador.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id_medicamento_editar']) && !empty($_POST['id_medicamento_editar'])) {
        ControladorMedicamentosAdmin::ctrEditarMedicamento();
    } else {
        ControladorMedicamentosAdmin::ctrCrearMedicamento();
    }
}

if (isset($_GET['idEliminar'])) {
    $controlador = new ControladorMedicamentosAdmin();
    $controlador->ctrEliminarMedicamento();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Medicamentos</title>
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
        <h1>Gestión de Medicamentos</h1>
        <div class="form-container">
            <form method="post" id="medicamento-form">
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
                    $medicamentos = ControladorMedicamentosAdmin::ctrMostrarMedicamentos(null, null);
                    if (is_array($medicamentos) && !empty($medicamentos)) {
                        foreach ($medicamentos as $medicamento):
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($medicamento["id_medicamento"]) ?></td>
                            <td><?= htmlspecialchars($medicamento["nombre_medicamento"]) ?></td>
                            <td><?= htmlspecialchars($medicamento["descripcion_medicamento"]) ?></td>
                            <td>
                                <button class="btn btn-warning btn-editar"
                                        data-id="<?= $medicamento["id_medicamento"] ?>"
                                        data-nombre="<?= htmlspecialchars($medicamento["nombre_medicamento"]) ?>"
                                        data-descripcion="<?= htmlspecialchars($medicamento["descripcion_medicamento"]) ?>">
                                    Editar
                                </button>
                                <a href="gestion_medicamentos.php?idEliminar=<?= $medicamento["id_medicamento"] ?>"
                                   class="btn btn-danger" onclick="return confirm('¿Estás seguro de que quieres desactivar este medicamento?')">
                                   Eliminar
                                </a>
                            </td>
                        </tr>
                        <?php
                        endforeach;
                    } else {
                        echo '<tr><td colspan="4">No hay medicamentos registrados.</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
            <div class="export-button" style="margin-top: 20px; text-align: right;">
                <a href="../../../controllers/admin/HC/exportar_medicamentos.php" class="btn btn-success">
                    <i class="fas fa-file-excel"></i> Exportar a Excel
                </a>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
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
                document.getElementById('id_medicamento_editar').value = '';
                document.getElementById('medicamento-form').reset();
                document.getElementById('btn-cancelar').style.display = 'none';
            });
        });
    </script>
</body>
</html>
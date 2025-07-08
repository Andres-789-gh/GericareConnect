<?php
require_once __DIR__ . '/../../../controllers/auth/verificar_sesion.php';
require_once __DIR__ . '/../../../models/clases/actividad.php';
require_once __DIR__ . '/../../../models/clases/usuario.php';

verificarAcceso(['Administrador']);

// --- Lógica del Reporte ---
$modelo_actividad = new Actividad();
$modelo_usuario = new usuario();

// Obtener la lista de todos los cuidadores para el filtro
$cuidadores = $modelo_usuario->obtenerUsuariosPorRol('Cuidador');

// Capturar los filtros de la URL
$id_cuidador_filtro = $_GET['cuidador'] ?? '';
$busqueda = $_GET['busqueda'] ?? '';
$estado_filtro = $_GET['estado'] ?? '';

$actividades = [];
// Si se ha seleccionado un cuidador, se buscan sus actividades
if (!empty($id_cuidador_filtro)) {
    $actividades = $modelo_actividad->consultarPorCuidador($id_cuidador_filtro, $busqueda, $estado_filtro);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Actividades por Cuidador</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="stylesheet" href="../css_admin/historia_clinica_lista.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .search-container form {
            display: flex;
            gap: 15px;
            flex-wrap: wrap; /* Para que se ajuste en pantallas pequeñas */
        }
        .search-container .form-group {
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }
        .search-container label {
            font-size: 0.9em;
            color: #555;
            margin-bottom: 5px;
        }
        .search-container input, .search-container select {
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1rem;
            outline: none;
        }
        .search-container input {
             min-width: 250px; /* Ancho mínimo para el campo de búsqueda */
        }
        .search-container button {
            align-self: flex-end; /* Alinear el botón con la parte inferior de los inputs */
        }
        .report-subtitle {
            text-align: center;
            color: #6c757d;
            margin-top: 2rem;
            font-style: italic;
        }
    </style>
</head>
<body>
    <header class="admin-header">
        <div class="logo-container">
            <img src="../../imagenes/Geri_Logo-..png" alt="Logo" class="logo" onclick="window.location.href='admin_pacientes.php'">
            <span class="app-name">GERICARE CONNECT</span>
        </div>
        <nav>
            <ul>
                <li><a href="admin_pacientes.php"><i class="fas fa-user-injured"></i> Pacientes</a></li>
                <li><a href="historia_clinica.php"><i class="fas fa-file-medical"></i> Historias Clínicas</a></li>
                <li><a href="admin_actividades.php"><i class="fas fa-tasks"></i> Actividades</a></li>
                <li><a href="reporte_actividades_cuidador.php" class="active"><i class="fas fa-chart-line"></i> Reporte Actividades</a></li>
                <li><a href="../../../controllers/admin/logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a></li>
            </ul>
        </nav>
    </header>

    <main class="admin-content">
        <div class="historias-container">
            <h1><i class="fas fa-chart-line"></i> Reporte de Actividades por Cuidador</h1>
            
            <div class="search-container">
                <form method="GET">
                    <div class="form-group">
                        <label for="cuidador">Seleccione un Cuidador:</label>
                        <select name="cuidador" id="cuidador" onchange="this.form.submit()">
                            <option value="">-- Todos los Cuidadores --</option>
                            <?php foreach ($cuidadores as $cuidador): ?>
                                <option value="<?= $cuidador['id_usuario'] ?>" <?= ($id_cuidador_filtro == $cuidador['id_usuario']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cuidador['nombre'] . ' ' . $cuidador['apellido']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="estado">Filtrar por Estado:</label>
                        <select name="estado" id="estado" onchange="this.form.submit()">
                            <option value="">Todos</option>
                            <option value="Pendiente" <?= $estado_filtro == 'Pendiente' ? 'selected' : '' ?>>Pendientes</option>
                            <option value="Completada" <?= $estado_filtro == 'Completada' ? 'selected' : '' ?>>Completadas</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="busqueda">Buscar en resultados:</label>
                        <input type="search" name="busqueda" id="busqueda" placeholder="Por paciente o actividad..." value="<?= htmlspecialchars($busqueda) ?>">
                    </div>

                    <button type="submit"><i class="fas fa-search"></i> Filtrar</button>
                </form>
            </div>

            <?php if (!empty($id_cuidador_filtro)): ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Actividad</th>
                                <th>Paciente Asignado</th>
                                <th>Fecha</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($actividades)): ?>
                                <tr><td colspan="4">No se encontraron actividades para este cuidador con los filtros seleccionados.</td></tr>
                            <?php else: ?>
                                <?php foreach ($actividades as $actividad): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($actividad['tipo_actividad']) ?></td>
                                        <td><?= htmlspecialchars($actividad['nombre_paciente']) ?></td>
                                        <td><?= htmlspecialchars(date("d/m/Y", strtotime($actividad['fecha_actividad']))) ?></td>
                                        <td>
                                            <span class="estado-<?= strtolower(htmlspecialchars($actividad['estado_actividad'])) ?>">
                                                <?= htmlspecialchars($actividad['estado_actividad']) ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    <div style="text-align: right; margin-top: 20px;">
                        <a href="../../../controllers/admin/actividad/exportar_actividades_cuidador.php?cuidador=<?= htmlspecialchars($id_cuidador_filtro) ?>&estado=<?= htmlspecialchars($estado_filtro) ?>&busqueda=<?= htmlspecialchars($busqueda) ?>" class="btn-add" style="background-color: #1a73e8;">
                            <i class="fas fa-file-excel"></i> Exportar a Excel
                        </a>
                    </div>
                    </div>
                </div>
            <?php else: ?>
                <p class="report-subtitle">Por favor, seleccione un cuidador para generar el reporte.</p>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
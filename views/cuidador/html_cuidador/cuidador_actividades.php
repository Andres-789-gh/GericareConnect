<?php
require_once __DIR__ . '/../../../controllers/auth/verificar_sesion.php';
require_once __DIR__ . '/../../../models/clases/actividad.php';
verificarAcceso(['Cuidador']);

$busqueda = $_GET['busqueda'] ?? '';
$estado_filtro = $_GET['estado'] ?? '';

$modelo_actividad = new Actividad();
$actividades = $modelo_actividad->consultarPorCuidador($_SESSION['id_usuario'], $busqueda, $estado_filtro);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Actividades Asignadas</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="stylesheet" href="../../admin/css_admin/historia_clinica_lista.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .search-container form { display: flex; gap: 15px; }
        .search-container input, .search-container select { padding: 12px; border: 1px solid #ccc; border-radius: 5px; font-size: 1rem; outline: none; }
        .search-container input { flex-grow: 1; }
        .search-container select { background-color: #f8f9fa; }
        .btn-completar { background-color: #28a745; color: white; border: none; padding: 8px 12px; border-radius: 5px; cursor: pointer; font-size: 0.9em; transition: background-color 0.2s; }
        .btn-completar:hover { background-color: #218838; }

        /* Estilo para el botón de exportar */
        .btn-export {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            font-size: 1rem;
            font-weight: 500;
            color: white;
            background-color: #1D6F42; /* Verde oscuro de Excel */
            border: none;
            border-radius: 8px;
            text-decoration: none;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }
        .btn-export:hover {
            background-color: #165934;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <header class="admin-header">
        <div class="logo-container">
            <img src="../../imagenes/Geri_Logo-..png" alt="Logo" class="logo" onclick="window.location.href='cuidadores_panel_principal.php'">
            <span class="app-name">GERICARE CONNECT</span>
        </div>
        <nav>
            <ul>
                <li><a href="cuidadores_panel_principal.php"><i class="fas fa-chevron-left"></i> Volver</a></li>
                <li><a href="cuidador_actividades.php" class="active"><i class="fas fa-tasks"></i> Actividades</a></li>
                <li><a href="../../../controllers/cuidador/logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a></li>
            </ul>
        </nav>
    </header>

    <main class="admin-content">
        <div class="historias-container">
            <h1><i class="fas fa-tasks"></i> Actividades de mis Pacientes</h1>
            <div class="search-container">
                <form method="GET">
                    <select name="estado" onchange="this.form.submit()">
                        <option value=""> Todos los Estados </option>
                        <option value="Pendiente" <?= $estado_filtro == 'Pendiente' ? 'selected' : '' ?>>Pendientes</option>
                        <option value="Completada" <?= $estado_filtro == 'Completada' ? 'selected' : '' ?>>Completadas</option>
                    </select>
                    <input type="search" name="busqueda" placeholder="Buscar por paciente, documento o actividad..." value="<?= htmlspecialchars($busqueda) ?>">
                    <button type="submit"><i class="fas fa-search"></i></button>
                </form>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Actividad</th>
                            <th>Paciente</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                            <th>Completar</th> 
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($actividades)): ?>
                            <tr><td colspan="5">No hay actividades que coincidan con los filtros.</td></tr>
                        <?php else: ?>
                            <?php foreach ($actividades as $actividad): ?>
                                <tr>
                                    <td><?= htmlspecialchars($actividad['tipo_actividad']) ?></td>
                                    <td><?= htmlspecialchars($actividad['nombre_paciente']) ?></td>
                                    <td><?= htmlspecialchars(date("d/m/Y", strtotime($actividad['fecha_actividad']))) ?></td>
                                    <td><?= htmlspecialchars($actividad['estado_actividad']) ?></td>
                                    <td>
                                        <?php if ($actividad['estado_actividad'] == 'Pendiente'): ?>
                                            <button class="btn-completar" 
                                                    onclick="confirmarCompletar(
                                                        <?= $actividad['id_actividad'] ?>,
                                                        '<?= htmlspecialchars(addslashes($actividad['tipo_actividad']), ENT_QUOTES) ?>',
                                                        '<?= htmlspecialchars(addslashes($actividad['nombre_paciente']), ENT_QUOTES) ?>'
                                                    )">
                                                <i class="fas fa-check"></i> Completar
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
                 <div style="text-align: right; margin-top: 20px;">
                    <a href="../../../controllers/cuidador/actividad/exportar_actividades_cuidador.php?estado=<?= htmlspecialchars($estado_filtro) ?>&busqueda=<?= htmlspecialchars($busqueda) ?>" class="btn-export">
                        <i class="fas fa-file-excel"></i> Exportar a Excel
                    </a>
                </div>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            <?php if(isset($_SESSION['mensaje'])): ?>
                Swal.fire({ title: '¡Éxito!', text: '<?= addslashes($_SESSION['mensaje']) ?>', icon: 'success', confirmButtonColor: '#3085d6' });
                <?php unset($_SESSION['mensaje']); ?>
            <?php endif; ?>
            <?php if(isset($_SESSION['error'])): ?>
                Swal.fire({ title: 'Error', text: '<?= addslashes($_SESSION['error']) ?>', icon: 'error', confirmButtonColor: '#d33' });
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
        });

        function confirmarCompletar(id, nombreActividad, nombrePaciente) {
            Swal.fire({
                title: '¿Estas Seguro?',
                html: `¿Deseas marcar la actividad "<b>${nombreActividad}</b>" asignada a <b>${nombrePaciente}</b> como completada?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, ¡completar!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '../../../controllers/cuidador/actividad/completar_actividad_controller.php';
                    
                    const hiddenFieldId = document.createElement('input');
                    hiddenFieldId.type = 'hidden';
                    hiddenFieldId.name = 'id_actividad';
                    hiddenFieldId.value = id;
                    form.appendChild(hiddenFieldId);

                    document.body.appendChild(form);
                    form.submit();
                }
            })
        }
    </script>
</body>
</html>
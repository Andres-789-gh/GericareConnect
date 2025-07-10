<?php
require_once __DIR__ . '/../../../controllers/auth/verificar_sesion.php';
require_once __DIR__ . '/../../../models/clases/actividad.php';
verificarAcceso(['Administrador']);

// Capturar los filtros de la URL
$busqueda_inicial = $_GET['busqueda'] ?? '';
$estado_inicial = $_GET['estado'] ?? '';
$modelo_actividad = new Actividad();

// Pasar los filtros al método de consulta
$actividades = $modelo_actividad->consultar($busqueda_inicial, $estado_inicial);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Actividades</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="stylesheet" href="../css_admin/historia_clinica_lista.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .search-container form { display: flex; gap: 15px; }
        .search-container input, .search-container select { padding: 12px; border: 1px solid #ccc; border-radius: 5px; font-size: 1rem; outline: none; }
        .search-container input { flex-grow: 1; }
        .search-container select { background-color: #f8f9fa; }

        /* ===== ESTILO PARA EL NUEVO BOTÓN DE REPORTE ===== */
        .btn-report {
            background-color: #007bff; /* Color azul para reportes/información */
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 500;
            transition: background-color 0.3s, transform 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-report:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
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
                <li><a href="admin_actividades.php" class="active"><i class="fas fa-tasks"></i> Actividades</a></li>
                <li><a href="../../../controllers/admin/logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a></li>
            </ul>
        </nav>
        <div class="add-button-container">
            <a href="reporte_actividades_cuidador.php" class="btn-report"><i class="fas fa-chart-line"></i> Reporte Actividades</a>
            <a href="form_actividades.php" class="btn-add"><i class="fas fa-plus"></i> Nueva Actividad</a>
        </div>
        </header>

    <main class="admin-content">
        <div class="historias-container">
            <h1><i class="fas fa-tasks"></i> Actividades Programadas</h1>
            
            <div class="search-container">
                <form id="searchForm" onsubmit="return false;">
                    <select id="filtro_estado" name="estado">
                        <option value=""> Todos los Estados </option>
                        <option value="Pendiente" <?= $estado_inicial == 'Pendiente' ? 'selected' : '' ?>>Pendientes</option>
                        <option value="Completada" <?= $estado_inicial == 'Completada' ? 'selected' : '' ?>>Completadas</option>
                    </select>
                    <input id="termino_busqueda" type="search" name="busqueda" placeholder="Buscar por paciente, documento o tipo de actividad..." value="<?= htmlspecialchars($busqueda_inicial) ?>">
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
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($actividades)): ?>
                            <tr><td colspan="5">No se encontraron actividades.</td></tr>
                        <?php else: ?>
                            <?php foreach ($actividades as $actividad): ?>
                                <tr>
                                    <td><?= htmlspecialchars($actividad['tipo_actividad']) ?></td>
                                    <td><?= htmlspecialchars($actividad['nombre_paciente']) ?></td>
                                    <td><?= htmlspecialchars(date("d/m/Y", strtotime($actividad['fecha_actividad']))) ?></td>
                                    <td><?= htmlspecialchars($actividad['estado_actividad']) ?></td>
                                    <td class="actions">
                                        <?php if ($actividad['estado_actividad'] == 'Pendiente'): ?>
                                            <a href="form_actividades.php?id=<?= $actividad['id_actividad'] ?>" class="btn-action btn-edit" title="Editar"><i class="fas fa-edit"></i></a>
                                        <?php endif; ?>
                                        
                                        <button class="btn-action btn-delete" 
                                                onclick="confirmarDesactivacion(<?= $actividad['id_actividad'] ?>)" 
                                                title="Eliminar Actividad">
                                            <i class="fas fa-ban"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script>
        // Mensajes de éxito y error
        document.addEventListener('DOMContentLoaded', function() {
            <?php if(isset($_SESSION['mensaje'])): ?>
                Swal.fire({
                    title: '¡Éxito!',
                    text: '<?= addslashes($_SESSION['mensaje']) ?>',
                    icon: 'success',
                    confirmButtonColor: '#3085d6'
                });
                <?php unset($_SESSION['mensaje']); ?>
            <?php endif; ?>
            <?php if(isset($_SESSION['error'])): ?>
                Swal.fire({
                    title: 'Error',
                    text: '<?= addslashes($_SESSION['error']) ?>',
                    icon: 'error',
                    confirmButtonColor: '#d33'
                });
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
        });

        // Función para la confirmación de eliminar
        function confirmarDesactivacion(id) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "La actividad se eliminara.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: '¡Sí, eliminar!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '../../../controllers/admin/actividad/actividad_controller.php';
                    
                    const hiddenFieldAccion = document.createElement('input');
                    hiddenFieldAccion.type = 'hidden';
                    hiddenFieldAccion.name = 'accion';
                    hiddenFieldAccion.value = 'eliminar';
                    form.appendChild(hiddenFieldAccion);

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
    <script src="../js_admin/buscar_actividad_admin.js"></script>
</body>
</html>
<?php
require_once __DIR__ . '/../../../controllers/auth/verificar_sesion.php';
require_once __DIR__ . '/../../../models/clases/actividad.php';
$titulo_pagina = 'Pacientes Asignados';
include 'header_cuidador.php'; // Incluye el nuevo header

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
          /* Forzamos al header a estar siempre en la capa superior */
        header.header-cuidador {
            position: relative;
            z-index: 1000 !important; 
        }

        /* Forzamos al contenido principal a estar en una capa inferior */
        main.main-content {
            position: relative;
            z-index: 1 !important;
        }
        /* --- Contenedor Principal --- */
    .historias-container {
        background-color: #fff;
        padding: 2rem;
        border-radius: 12px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        margin: 0;
    }

    .historias-container h1 {
        font-size: 2rem;
        font-weight: 700;
        color: #343a40;
        margin: 0 0 1.5rem 0;
        display: flex;
        align-items: center;
        gap: 1rem;
        border-bottom: 3px solid #F57C00;
        padding-bottom: 0.8rem;
    }

    /* --- Barra de Búsqueda y Filtros (CON LUPA NARANJA) --- */
    .search-container {
        margin-bottom: 2rem;
    }

    .search-container form {
        display: flex;
        width: 100%;
    }

    .search-container form > * {
        border: 1px solid #ced4da;
        padding: 12px 15px;
        font-size: 1rem;
        outline: none;
        margin: 0;
        height: 50px;
        box-sizing: border-box;
    }

    .search-container select {
        background-color: #f8f9fa;
        border-top-left-radius: 8px;
        border-bottom-left-radius: 8px;
        border-right-width: 0;
    }

    .search-container input[type="search"] {
        flex-grow: 1;
        border-radius: 0;
    }
    
    /* === ESTILO CORREGIDO DEL BOTÓN DE LA LUPA === */
    .search-container button[type="submit"] {
        background-color: #F57C00;  /* ¡FONDO NARANJA! */
        color: white;               /* ¡ÍCONO BLANCO! */
        border-color: #F57C00;      /* Borde del mismo color */
        border-top-right-radius: 8px;
        border-bottom-right-radius: 8px;
        cursor: pointer;
        transition: background-color 0.2s;
        flex-shrink: 0;
    }

    .search-container button[type="submit"]:hover {
        background-color: #ef6c00; /* Naranja más oscuro al pasar el mouse */
    }

    .search-container form:focus-within {
        box-shadow: 0 0 0 3px rgba(245, 124, 0, 0.25);
        border-radius: 8px;
    }

    /* --- Estilos de la Tabla --- */
    .table-container { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; font-size: 0.95rem; }
    table thead th { background-color: #F57C00; color: white; font-weight: 600; padding: 15px; text-align: left; text-transform: uppercase; letter-spacing: 0.5px; }
    table thead th:first-child { border-top-left-radius: 8px; }
    table thead th:last-child { border-top-right-radius: 8px; }
    table tbody tr { border-bottom: 1px solid #f1f1f1; transition: background-color 0.2s ease; }
    table tbody tr:last-child { border-bottom: none; }
    table tbody tr:hover { background-color: #fff8e1; }
    table tbody td { padding: 15px; vertical-align: middle; color: #555; }
    table tbody td:nth-child(4) { font-weight: 600; }
    
    /* --- Estilos de los Botones --- */
    .btn-completar { background-color: #28a745; color: white; border: none; border-radius: 50px; padding: 8px 15px; font-size: 0.9em; font-weight: 500; cursor: pointer; transition: all 0.2s ease; box-shadow: 0 2px 5px rgba(40, 167, 69, 0.3); }
    .btn-completar:hover { background-color: #218838; transform: translateY(-1px); }
    .btn-export { display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; font-size: 1rem; font-weight: 500; color: white; background-color: #1D6F42; border: none; border-radius: 8px; text-decoration: none; cursor: pointer; transition: all 0.3s ease; }
    .btn-export:hover { background-color: #165934; transform: translateY(-2px); }
</style>
</head>
<body>
   

<script src="../js_cuidador/cuidadores_panel_principal.js" defer></script>
<?php include 'footer_cuidador.php'; // Incluye el nuevo footer ?>
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
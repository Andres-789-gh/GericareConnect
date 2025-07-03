<?php
session_start();

// --- SEGURIDAD: VERIFICACIÓN DE ROL DE ADMINISTRADOR ---
if (!isset($_SESSION['id_usuario']) || $_SESSION['nombre_rol'] !== 'Administrador') {
    // Si no es admin, lo redirige al login.
    header("Location: /GericareConnect/views/index-login/htmls/index.html");
    exit();
}

// --- LÓGICA PARA OBTENER LOS PACIENTES ---
// Se incluye la clase Paciente para poder usar sus funciones.
require_once __DIR__ . '/../../../models/clases/pacientes.php';

// Se crea un objeto del modelo de Pacientes.
$pacienteModel = new Paciente();
// Se obtienen todos los pacientes de la base de datos.
$lista_de_pacientes = $pacienteModel->consultar();

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Pacientes - GeriCare Connect</title>
    <link rel="stylesheet" href="../css_admin/admin_pacientes1.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

    <header class="admin-header">
        <div class="logo-container">
            <img src="../../../imagenes/Geri_Logo-..png" alt="Logo GeriCare" class="logo">
            <span class="app-name">GERICARE CONNECT</span>
        </div>
        <nav>
            <ul>
                <li><a href="admin_pacientes.php" class="active"><i class="fas fa-user-injured"></i> Pacientes</a></li>
                <li><a href="admin_solicitudes.html"><i class="fas fa-envelope-open-text"></i> Solicitudes</a></li>
                <li><a href="../../../controllers/admin/logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a></li>
            </ul>
        </nav>
    </header>

    <main class="admin-content">
        <div class="pacientes-container">
            <h1><i class="fas fa-users-cog"></i> Gestión de Pacientes</h1>
            <div class="toolbar" style="margin-bottom: 20px;">
                <a href="form_paciente.php" class="add-paciente-button"><i class="fas fa-user-plus"></i> Agregar Nuevo Paciente</a>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre Completo</th>
                            <th>Documento</th>
                            <th>Fecha Nacimiento</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Si no hay pacientes registrados, se muestra un mensaje amigable.
                        if (empty($lista_de_pacientes)) {
                            echo '<tr><td colspan="5" style="text-align:center; padding: 20px;">No hay pacientes registrados todavía.</td></tr>';
                        } else {
                            // Si hay pacientes, se recorren y se muestra una fila por cada uno.
                            foreach ($lista_de_pacientes as $paciente) {
                                echo '<tr>';
                                echo '    <td>' . htmlspecialchars($paciente['id_paciente']) . '</td>';
                                echo '    <td>' . htmlspecialchars($paciente['nombre'] . ' ' . $paciente['apellido']) . '</td>';
                                echo '    <td>' . htmlspecialchars($paciente['documento_identificacion']) . '</td>';
                                echo '    <td>' . htmlspecialchars($paciente['fecha_nacimiento']) . '</td>';
                                echo '    <td class="actions">';
                                // Botón para Editar: lleva al formulario con el ID del paciente.
                                echo '        <a href="form_paciente.php?id=' . $paciente['id_paciente'] . '" class="btn-action btn-edit" title="Editar"><i class="fas fa-edit"></i></a>';
                                // Botón para Desactivar: usa JavaScript para confirmar la acción.
                                echo '        <button class="btn-action btn-delete" onclick="confirmarDesactivacion(' . $paciente['id_paciente'] . ')" title="Desactivar"><i class="fas fa-trash-alt"></i></button>';
                                echo '    </td>';
                                echo '</tr>';
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <form id="delete-form" action="../../../controllers/admin/paciente_controller.php" method="POST" style="display:none;">
        <input type="hidden" name="accion" value="desactivar">
        <input type="hidden" id="delete-paciente-id" name="id_paciente">
    </form>

    <script>
    // Función de JavaScript para confirmar la desactivación con SweetAlert2
    function confirmarDesactivacion(pacienteId) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "El paciente será desactivado y no aparecerá en las listas principales.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, ¡desactivar!',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Si el usuario confirma, se asigna el ID al formulario oculto y se envía.
                document.getElementById('delete-paciente-id').value = pacienteId;
                document.getElementById('delete-form').submit();
            }
        });
    }

    // Código para mostrar notificaciones de éxito o error al cargar la página.
    document.addEventListener('DOMContentLoaded', function() {
        <?php if(isset($_SESSION['mensaje'])): ?>
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: '<?= addslashes($_SESSION['mensaje']) ?>',
                timer: 3000,
                showConfirmButton: false
            });
            <?php unset($_SESSION['mensaje']); ?>
        <?php endif; ?>

        <?php if(isset($_SESSION['error'])): ?>
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: '<?= addslashes($_SESSION['error']) ?>'
            });
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
    });
    </script>
</body>
</html>
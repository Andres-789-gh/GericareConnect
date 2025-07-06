<?php
require_once __DIR__ . '/../../../controllers/auth/verificar_sesion.php';
require_once __DIR__ . '/../../../models/clases/historia_clinica.php';

verificarAcceso(['Administrador']);

// Lógica para manejar la desactivación
if (isset($_GET['desactivar_id'])) {
    $modelo = new HistoriaClinica();
    try {
        $modelo->desactivarHistoria($_GET['desactivar_id']);
        $_SESSION['mensaje'] = "Historia clínica desactivada correctamente.";
    } catch (Exception $e) {
        $_SESSION['error'] = "Error al desactivar la historia clínica: " . $e->getMessage();
    }
    // Redirigir para limpiar la URL
    header("Location: historia_clinica.php");
    exit();
}

// Obtener la lista de historias (con o sin búsqueda)
$busqueda = $_GET['busqueda'] ?? '';
$modelo = new HistoriaClinica();
$historias = $modelo->consultarHistorias($busqueda);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Historias Clínicas</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="stylesheet" href="../css_admin/historia_clinica_lista.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                <li><a href="historia_clinica.php" class="active"><i class="fas fa-file-medical"></i> Historias Clínicas</a></li>
                <li><a href="../../../controllers/admin/logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a></li>
            </ul>
        </nav>
        <div class="add-button-container">
            <a href="form_historia_clinica.php" class="btn-add"><i class="fas fa-plus"></i> Crear Historia Clínica</a>
        </div>
    </header>

    <main class="admin-content">
        <div class="historias-container">
            <h1><i class="fas fa-book-medical"></i> Historias Clínicas Registradas</h1>

            <div class="search-container">
                <form method="GET" action="historia_clinica.php">
                    <input type="search" name="busqueda" placeholder="Buscar por nombre o documento del paciente..." value="<?= htmlspecialchars($busqueda) ?>">
                    <button type="submit"><i class="fas fa-search"></i></button>
                </form>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Paciente</th>
                            <th>Última Consulta</th>
                            <th>Estado de Salud General</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="historias-clinicas-tbody">
                        <?php if (empty($historias)): ?>
                            <tr>
                                <td colspan="5">No se encontraron historias clínicas.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($historias as $historia): ?>
                                <tr>
                                    <td><?= htmlspecialchars($historia['id_historia_clinica']) ?></td>
                                    <td><?= htmlspecialchars($historia['paciente_nombre_completo']) ?></td>
                                    <td><?= htmlspecialchars($historia['fecha_formateada']) ?></td>
                                    <td><?= htmlspecialchars(substr($historia['estado_salud'], 0, 50)) . '...' ?></td>
                                    <td class="actions">
                                        <a href="form_historia_clinica.php?id=<?= $historia['id_historia_clinica'] ?>" class="btn-edit" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="javascript:void(0);" onclick="confirmarDesactivacion(<?= $historia['id_historia_clinica'] ?>)" class="btn-delete" title="Desactivar">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
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
        // Script para mostrar mensajes de SweetAlert
        document.addEventListener('DOMContentLoaded', function() {
            <?php if(isset($_SESSION['mensaje'])): ?>
                Swal.fire({ icon: 'success', title: '¡Éxito!', text: '<?= addslashes($_SESSION['mensaje']) ?>', timer: 3000, showConfirmButton: false });
                <?php unset($_SESSION['mensaje']); ?>
            <?php endif; ?>
            <?php if(isset($_SESSION['error'])): ?>
                Swal.fire({ icon: 'error', title: 'Oops...', text: '<?= addslashes($_SESSION['error']) ?>' });
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
        });

        // Script para confirmar la desactivación
        function confirmarDesactivacion(id) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "Esta acción desactivará la historia clínica, pero no la eliminará permanentemente.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, ¡desactivar!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `historia_clinica.php?desactivar_id=${id}`;
                }
            });
        }
    </script>
</body>
</html>
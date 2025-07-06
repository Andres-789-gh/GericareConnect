<?php
// Incluir el controlador para acceder a las funciones de consulta
require_once __DIR__ . '/../../../controllers/admin/HC/historia_clinica_controlador.php';

// Crear una instancia del controlador para poder usar sus métodos
$controller = new HistoriaClinicaController();
// Obtener la lista inicial de todas las historias clínicas
$historias_clinicas = $controller->consultarHistoriasClinicas();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GeriCare Connect | Historias Clínicas</title>
    <link rel="stylesheet" href="../css_admin/historia_clinica_lista.css"> <!-- Se recomienda un CSS específico para la lista -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

</head>

<body>
    <div class="container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="logo">
                <img src="../../../../assets/img/logo.png" alt="Logo">
                <h2>GeriCare Connect</h2>
            </div>
            <nav class="menu">
                <a href="#"><i class="fas fa-home"></i> Dashboard</a>
                <a href="admin_pacientes.php"><i class="fas fa-user-injured"></i> Pacientes</a>
                <a href="historia_clinica.php" class="active"><i class="fas fa-file-medical"></i> Historia Clínica</a>
                <a href="agendar_citas.php"><i class="fas fa-calendar-alt"></i> Agendar Citas</a>
                <a href="registrar_empleado.php"><i class="fas fa-user-plus"></i> Registrar Empleados</a>
                <a href="admin_solicitudes.php"><i class="fas fa-tasks"></i> Solicitudes</a>
                <a href="../../../../controllers/admin/logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="main-header">
                <h1>Gestión de Historias Clínicas</h1>
                <div class="header-actions">
                    <a href="form_historia_clinica.php" class="btn-register">
                        <i class="fas fa-plus-circle"></i> Registrar Nueva Historia
                    </a>
                </div>
            </header>

            <div class="content">
                <div class="search-container">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchInput" placeholder="Buscar por nombre, apellido o cédula del paciente...">
                </div>

                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID Historia</th>
                                <th>Paciente</th>
                                <th>Fecha de Creación</th>
                                <th>Última Modificación</th>
                                <th>Motivo de la Consulta</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="historias-clinicas-tbody">
                            <?php if (!empty($historias_clinicas)) : ?>
                                <?php foreach ($historias_clinicas as $historia) : ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($historia['id_historia_clinica']); ?></td>
                                        <td><?php echo htmlspecialchars($historia['nombre'] . ' ' . $historia['apellido']); ?></td>
                                        <td><?php echo htmlspecialchars($historia['fecha_creacion']); ?></td>
                                        <td><?php echo htmlspecialchars($historia['fecha_actualizacion']); ?></td>
                                        <td><?php echo htmlspecialchars(substr($historia['motivo_consulta'], 0, 50)) . '...'; ?></td>
                                        <td class="actions">
                                            <a href="form_historia_clinica.php?id=<?php echo $historia['id_historia_clinica']; ?>" class="btn-edit">
                                                <i class="fas fa-edit"></i> Editar
                                            </a>
                                            <!-- Se puede agregar un botón para eliminar con confirmación -->
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="6">No hay historias clínicas registradas.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <script src="../js_admin/historia_clinica.js"></script>
</body>

</html>

<?php
require_once __DIR__ . "/../../../controllers/admin/HC/enfermedad.controlador.php";
// ... (Toda tu lógica de POST y GET para crear/editar/eliminar)
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Enfermedades - GeriCare</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="../libs/bootstrap/css/bootstrap.min.css">
    
    <link rel="stylesheet" href="../css_admin/admin_main.css?v=<?= time(); ?>">
    <link rel="stylesheet" href="../css_admin/gestion_catalogos.css?v=<?= time(); ?>">
</head>
<body>
    <header class="header">
        </header>

    <main class="container-fluid mt-5">
        <h1 class="text-center mb-5"><i class="fas fa-viruses"></i> Gestión de Enfermedades</h1>

        <div class="gestion-container">
            <div class="form-section">
                <form method="post" id="enfermedad-form" class="form-container-card">
                    <h2 id="form-title" class="form-title-main">Agregar Nueva Enfermedad</h2>
                    <input type="hidden" id="id_enfermedad_editar" name="id_enfermedad_editar">
                    
                    <div class="mb-3">
                        <label for="nombre_enfermedad" class="form-label">Nombre de la Enfermedad</label>
                        <input type="text" id="nombre_enfermedad" name="nombre_enfermedad" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="descripcion_enfermedad" class="form-label">Descripción</label>
                        <textarea id="descripcion_enfermedad" name="descripcion_enfermedad" rows="4" class="form-control"></textarea>
                    </div>

                    <div class="form-actions">
                        <button type="button" id="btn-cancelar" class="btn-action-cancel" style="display: none;">Cancelar Edición</button>
                        <button type="submit" class="btn-action-submit"><i class="fas fa-save"></i> Guardar</button>
                    </div>
                </form>
            </div>

            <div class="table-section">
                <div class="d-flex justify-content-between align-items-center">
                    <h2>Listado Existente</h2>
                    <a href="#" class="btn-export"><i class="fas fa-file-excel"></i> Exportar a Excel</a>
                </div>
                <div class="table-responsive mt-3">
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
    </body>
</html>
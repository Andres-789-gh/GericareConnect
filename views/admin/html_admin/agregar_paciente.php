<?php
require_once __DIR__ . '/../../../controllers/auth/verificar_sesion.php';
verificarAcceso(['Administrador']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Paciente - GeriCare Connect</title>
    
    <!-- Iconos de Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    
    <!-- Fuentes de Google -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Estilos CSS Integrados -->
    <style>
        /* --- Reset y Estilos Base --- */
        :root {
            --primary-color: #28a745; /* Verde principal */
            --primary-hover: #218838; /* Verde más oscuro para hover */
            --light-gray: #f4f7f6;
            --medium-gray: #e9ecef;
            --dark-gray: #6c757d;
            --text-color: #343a40;
            --white: #ffffff;
            --danger-bg: #f8d7da;
            --danger-text: #721c24;
            --danger-border: #f5c6cb;
            --success-bg: #d4edda;
            --success-text: #155724;
            --success-border: #c3e6cb;
            --shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--light-gray);
            color: var(--text-color);
            line-height: 1.6;
        }

        /* --- Cabecera --- */
        .admin-header {
            background-color: var(--white);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: var(--shadow);
        }
        .logo-container {
            display: flex;
            align-items: center;
            cursor: pointer;
        }
        .logo {
            height: 50px;
            margin-right: 15px;
        }
        .app-name {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
        }
        .admin-header nav ul {
            list-style: none;
            display: flex;
            gap: 1.5rem;
        }
        .admin-header nav a {
            text-decoration: none;
            color: var(--dark-gray);
            font-weight: 500;
            transition: color 0.3s ease;
        }
        .admin-header nav a:hover,
        .admin-header nav a.active {
            color: var(--primary-color);
        }

        /* --- Contenido Principal y Formulario --- */
        .admin-content {
            padding: 2rem;
        }
        .agregar-paciente-container {
            max-width: 900px;
            margin: 0 auto;
            background: var(--white);
            padding: 2.5rem;
            border-radius: 12px;
            box-shadow: var(--shadow);
        }
        h1 {
            font-size: 1.8rem;
            font-weight: 600;
            color: var(--text-color);
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        /* --- Estilo de la Rejilla del Formulario --- */
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr; /* Dos columnas */
            gap: 1.5rem 2rem; /* Espacio vertical y horizontal */
            margin-bottom: 2rem;
        }

        /* Responsive: una columna en pantallas pequeñas */
        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
        }
        
        /* --- Estilo de los Campos del Formulario (Inputs, Selects) --- */
        .form-group {
            display: flex;
            flex-direction: column;
        }
        .form-group label {
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--dark-gray);
            font-size: 0.9rem;
        }
        .form-group label i {
            margin-right: 8px;
            color: var(--primary-color);
        }
        
        /* Estilo unificado para inputs de texto, número y fecha */
        input[type="text"],
        input[type="number"],
        input[type="date"],
        select {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--medium-gray);
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            font-size: 1rem;
            color: var(--text-color);
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
            appearance: none; /* Quita estilos nativos */
        }

        input:focus, select:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.2);
        }
        
        /* --- ESTILO ESPECIAL PARA EL CAMPO DE FECHA --- */
        input[type="date"] {
            position: relative;
            background-color: var(--white);
            cursor: text;
        }
        /* Color del texto del placeholder (ej. dd/mm/aaaa) */
        input[type="date"]::-webkit-datetime-edit { 
            color: #757575; 
        }
        /* Estilo del ícono del calendario (para Chrome/Safari) */
        input[type="date"]::-webkit-calendar-picker-indicator {
            opacity: 0; /* Oculta el ícono original */
            cursor: pointer;
            position: absolute;
            right: 0;
            top: 0;
            width: 100%;
            height: 100%;
        }
        .form-group.date-group {
            position: relative;
        }
        .form-group.date-group::after {
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            content: "\f073"; /* Ícono de calendario de Font Awesome */
            color: var(--dark-gray);
            position: absolute;
            right: 15px;
            top: 42px; /* Ajuste vertical */
            pointer-events: none; /* Para que el clic llegue al input */
        }
        
        /* --- Estilo para el Select --- */
        .form-group.select-group {
            position: relative;
        }
        .form-group.select-group::after {
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            content: "\f078"; /* Ícono de flecha hacia abajo */
            color: var(--dark-gray);
            font-size: 0.8rem;
            position: absolute;
            right: 15px;
            top: 44px; /* Ajuste vertical */
            pointer-events: none;
        }

        /* --- Botón de Guardar --- */
        .submit-button {
            width: 100%;
            padding: 15px;
            background: linear-gradient(45deg, var(--primary-color), #24c251);
            color: var(--white);
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }
        .submit-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(40, 167, 69, 0.3);
        }
        .submit-button:disabled {
            background: var(--medium-gray);
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        /* --- Contenedores de Feedback (Errores y Éxito) --- */
        #feedback-container { margin-bottom: 1.5rem; text-align: left; }
        .error-box, .success-box {
            padding: 15px;
            border-radius: 8px;
            font-size: 0.95rem;
        }
        .error-box {
            background-color: var(--danger-bg);
            color: var(--danger-text);
            border: 1px solid var(--danger-border);
        }
        .success-box {
            background-color: var(--success-bg);
            color: var(--success-text);
            border: 1px solid var(--success-border);
        }

    </style>
</head>
<body>
    <header class="admin-header">
        <div class="logo-container" onclick="window.location.href='admin_pacientes.html'">
            <img src="../../imagenes/Geri_Logo-..png" alt="Logo GeriCare Connect" class="logo">
            <span class="app-name">GERICARE CONNECT</span>
        </div>
        <nav>
            <ul>
                <li><a href="admin_pacientes.html"><i class="fas fa-user-injured"></i> Pacientes</a></li>
                <li><a href="admin_solicitudes.php"><i class="fas fa-envelope-open-text"></i> Solicitudes</a></li>
                <li><a href="../../../controllers/admin/logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a></li>
            </ul>
        </nav>
    </header>

    <main class="admin-content">
        <div class="agregar-paciente-container">
            <h1><i class="fas fa-user-plus"></i> Agregar Nuevo Paciente</h1>

            <div id="feedback-container"></div>

            <form id="formulario-agregar-paciente" class="agregar-paciente-form" novalidate>
                <!-- Campos ocultos para IDs -->
                <input type="hidden" id="solicitud_origen_id" name="solicitud_origen_id">
                <input type="hidden" id="familiar_solicitante_id" name="familiar_solicitante_id">
                
                <div class="form-grid">
                    <!-- Columna 1 -->
                    <div>
                        <div class="form-group">
                            <label for="nombre"><i class="fas fa-user"></i> Nombres</label>
                            <input type="text" id="nombre" name="nombre" required>
                        </div>
                        <div class="form-group">
                            <label for="apellido"><i class="fas fa-user-friends"></i> Apellidos</label>
                            <input type="text" id="apellido" name="apellido" required>
                        </div>
                        <div class="form-group">
                            <label for="documento_identificacion"><i class="fas fa-id-card"></i> Número de Documento</label>
                            <input type="number" id="documento_identificacion" name="documento_identificacion" required>
                        </div>
                        <div class="form-group date-group">
                            <label for="fecha_nacimiento"><i class="fas fa-calendar-alt"></i> Fecha de Nacimiento</label>
                            <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" required>
                        </div>
                         <div class="form-group select-group">
                            <label for="genero"><i class="fas fa-venus-mars"></i> Género</label>
                            <select id="genero" name="genero" required>
                                <option value="">Seleccione...</option>
                                <option value="Masculino">Masculino</option>
                                <option value="Femenino">Femenino</option>
                            </select>
                        </div>
                    </div>
                    <!-- Columna 2 -->
                    <div>
                        <div class="form-group">
                            <label for="contacto_emergencia"><i class="fas fa-phone-alt"></i> Contacto de Emergencia</label>
                            <input type="text" id="contacto_emergencia" name="contacto_emergencia">
                        </div>
                        <div class="form-group select-group">
                            <label for="estado_civil"><i class="fas fa-ring"></i> Estado Civil</label>
                             <select id="estado_civil" name="estado_civil" required>
                                <option value="">Seleccione...</option>
                                <option value="Soltero/a">Soltero/a</option>
                                <option value="Casado/a">Casado/a</option>
                                <option value="Viudo/a">Viudo/a</option>
                                <option value="Divorciado/a">Divorciado/a</option>
                                <option value="Unión Libre">Unión Libre</option>
                            </select>
                        </div>
                        <div class="form-group select-group">
                            <label for="tipo_sangre"><i class="fas fa-tint"></i> Tipo de Sangre</label>
                            <select id="tipo_sangre" name="tipo_sangre" required>
                                <option value="">Seleccione...</option>
                                <option value="A+">A+</option><option value="A-">A-</option>
                                <option value="B+">B+</option><option value="B-">B-</option>
                                <option value="AB+">AB+</option><option value="AB-">AB-</option>
                                <option value="O+">O+</option><option value="O-">O-</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="seguro_medico"><i class="fas fa-file-medical"></i> Seguro Médico</label>
                            <input type="text" id="seguro_medico" name="seguro_medico">
                        </div>
                        <div class="form-group">
                            <label for="numero_seguro"><i class="fas fa-hashtag"></i> Número de Seguro</label>
                            <input type="text" id="numero_seguro" name="numero_seguro">
                        </div>
                    </div>
                </div>

                <button type="submit" class="submit-button">
                    <i class="fas fa-save"></i> Guardar Paciente
                </button>
            </form>
        </div>
    </main>

    <script>
        document.getElementById('formulario-agregar-paciente').addEventListener('submit', function(event) {
            event.preventDefault(); 
            enviarFormulario();
        });

        function enviarFormulario() {
            const formulario = document.getElementById('formulario-agregar-paciente');
            const feedbackContainer = document.getElementById('feedback-container');
            const formData = new FormData(formulario);
            const submitButton = formulario.querySelector('.submit-button');

            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
            feedbackContainer.innerHTML = '';

            fetch('../../../controllers/admin/agregar_paciente_procesar.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    feedbackContainer.innerHTML = `<div class="success-box">${data.message}</div>`;
                    formulario.reset();
                    setTimeout(() => {
                        window.location.href = 'admin_pacientes.html';
                    }, 2500);
                } else {
                    feedbackContainer.innerHTML = `<div class="error-box"><b>Error al guardar:</b><br>${data.message}</div>`;
                }
            })
            .catch(error => {
                console.error('Error en fetch:', error);
                feedbackContainer.innerHTML = `<div class="error-box">Error de conexión. Revisa la consola (F12) para más detalles.</div>`;
            })
            .finally(() => {
                submitButton.disabled = false;
                submitButton.innerHTML = '<i class="fas fa-save"></i> Guardar Paciente';
            });
        }
    </script>
</body>
</html>

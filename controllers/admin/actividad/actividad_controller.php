<?php
// Inicia la sesión para poder usar variables como $_SESSION['mensaje'] y $_SESSION['error'].
session_start();

// Incluye los scripts necesarios: uno para seguridad y otro para la lógica de la base de datos.
require_once __DIR__ . '/../../../controllers/auth/verificar_sesion.php';
require_once __DIR__ . '/../../../models/clases/actividad.php';

// Protege la página: solo los usuarios con el rol 'Administrador' pueden ejecutar este script.
verificarAcceso(['Administrador']);

// Define las rutas a las que se redirigirá al usuario después de una operación.
$redirect_location = '/GericareConnect/views/admin/html_admin/admin_actividades.php'; // A la lista de actividades.
$form_location = '/GericareConnect/views/admin/html_admin/form_actividades.php';      // Al formulario.

// Si no se envió una 'accion' (registrar, actualizar, etc.), no hay nada que hacer.
// Se redirige al usuario a la lista principal de actividades para evitar errores.
if (!isset($_POST['accion'])) {
    header("Location: $redirect_location");
    exit();
}

// El bloque try...catch se usa para manejar errores de forma controlada.
// Si algo falla dentro del 'try', la ejecución salta al 'catch' sin detener la aplicación bruscamente.
try {
    // Crea un objeto del modelo 'Actividad' para acceder a sus funciones (registrar, eliminar, etc.).
    $modelo = new Actividad();
    // Guarda la acción que se recibió del formulario (ej: 'registrar', 'actualizar', 'eliminar').
    $accion = $_POST['accion'];

    // --- Lógica para ELIMINAR una actividad ---
    if ($accion === 'eliminar') {
        // Llama a la función 'eliminar' del modelo, pasándole el ID de la actividad a eliminar.
        $modelo->eliminar($_POST['id_actividad']);
        // Guarda un mensaje de éxito en la sesión para mostrarlo en la siguiente página.
        $_SESSION['mensaje'] = "Actividad eliminada correctamente.";
        // Redirige al usuario a la lista de actividades.
        header("Location: $redirect_location");
        // Detiene la ejecución del script.
        exit();
    }

    // --- Preparación de datos para REGISTRAR y ACTUALIZAR ---
    // Se crea un array '$datos' para organizar la información recibida del formulario.
    $datos = [
        'id_paciente'           => $_POST['id_paciente'],
        'tipo_actividad'        => $_POST['tipo_actividad'],
        'descripcion_actividad' => $_POST['descripcion_actividad'],
        'fecha_actividad'       => $_POST['fecha_actividad'],
        // Operador ternario: si el campo 'hora_inicio' no está vacío, usa su valor; si no, usa 'null'.
        // Esto previene errores si los campos de hora se dejan en blanco.
        'hora_inicio'           => !empty($_POST['hora_inicio']) ? $_POST['hora_inicio'] : null,
        'hora_fin'              => !empty($_POST['hora_fin']) ? $_POST['hora_fin'] : null,
    ];

    // --- Lógica para REGISTRAR una nueva actividad ---
    if ($accion === 'registrar') {
        // Llama a la función 'registrar' del modelo, pasándole el array con los datos.
        $modelo->registrar($datos);
        // Guarda un mensaje de éxito en la sesión.
        $_SESSION['mensaje'] = "Actividad registrada con éxito.";
    
    // --- Lógica para ACTUALIZAR una actividad existente ---
    } elseif ($accion === 'actualizar') {
        // Añade el 'id_actividad' al array de datos, ya que es necesario para saber qué registro actualizar.
        $datos['id_actividad'] = $_POST['id_actividad'];
        // Llama a la función 'actualizar' del modelo.
        $modelo->actualizar($datos);
        // Guarda el mensaje de éxito.
        $_SESSION['mensaje'] = "Actividad actualizada correctamente.";
    }

    // Si todo salió bien (registrar o actualizar), redirige al usuario a la lista de actividades.
    header("Location: $redirect_location");
    exit();

// --- Manejo de ERRORES ---
} catch (Exception $e) {
    // Si ocurre un error, se captura aquí.
    
    // Se comprueba si el error es una 'PDOException', que es específica de la base de datos.
    if ($e instanceof PDOException) {
        // Si es así, se muestra un mensaje genérico para no exponer detalles técnicos.
        // Un error común de este tipo es intentar registrar un dato que debe ser único y ya existe.
        $_SESSION['error'] = "No se logro guardar la actividad. Verifique que los datos no estén duplicados.";
    } else {
        // Si es otro tipo de error, se muestra el mensaje específico del error.
        $_SESSION['error'] = "Error: " . $e->getMessage();
    }
    
    // --- Redirección en caso de ERROR ---
    // Se prepara una variable para añadir el ID a la URL si el error ocurrió durante una actualización.
    $id_param = ($_POST['accion'] === 'actualizar' && isset($_POST['id_actividad'])) ? '?id=' . $_POST['id_actividad'] : '';
    // Redirige de vuelta al formulario, manteniendo al usuario en la página donde ocurrió el error.
    // Si era una actualización, se añade el '?id=...' para que el formulario se cargue con los datos previos.
    header("Location: " . $form_location . $id_param);
    exit();
}
?>
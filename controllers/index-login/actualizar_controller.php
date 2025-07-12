<?php
// VERIFICACIÓN DE SESIÓN Y PERMISOS

// 'require_once' carga el archivo que verifica si el usuario ha iniciado sesión.
// Si no ha iniciado sesión, este archivo lo redirigirá automáticamente a la página de login.
require_once __DIR__ . '/../auth/verificar_sesion.php';
verificarAcceso(); // Esta función se asegura de que solo usuarios logueados puedan acceder a este controlador.

// Se carga la clase 'usuario' para poder usar sus métodos.
require_once (__DIR__ . '/../../models/clases/usuario.php');

// DEFINIR A DÓNDE VOLVER 

// Se define una URL de retorno por defecto. Si algo sale mal, se redirige al usuario.
$url_retorno = '../../views/index-login/htmls/index.php'; 

// Se revisa el rol guardado en la sesión para definir una URL de retorno específica.
if (isset($_SESSION['nombre_rol'])) {
    switch ($_SESSION['nombre_rol']) {
        case 'Administrador':
            $url_retorno = '../../views/admin/html_admin/admin_pacientes.php';
            break;
        case 'Cuidador':
            $url_retorno = '../../views/cuidador/html_cuidador/cuidadores_panel_principal.php';
            break;
        case 'Familiar':
            $url_retorno = '../../views/familiar/html_familiar/familiares.php';
            break;
    }
}

// Se crea un objeto de la clase 'usuario' para poder interactuar con la base de datos.
$usuario = new usuario();

// MANEJAR LA SOLICITUD GET (CUANDO SE PIDE EL FORMULARIO PARA EDITAR)

// Se comprueba si la página fue solicitada usando el método GET y si se pasó un 'id' en la URL.
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    try {
        // Se llama al método 'obtenerPorId' del modelo para traer los datos del usuario desde la BD.
        $datosUsuario = $usuario->obtenerPorId($_GET['id']);
        
        // Si no se encuentra ningún usuario con ese ID, se crea un mensaje de error.
        if (!$datosUsuario) {
            $_SESSION['error'] = "Usuario no encontrado.";
            header("Location:" . $url_retorno); // Y se redirige a la página de retorno definida antes.
            exit;
        }

        // Si se encontró al usuario, se incluye el archivo de la vista 'actualizar_usuario.php'.
        // Esto rellena el formulario con los datos del usuario ($datosUsuario).
        include_once (__DIR__ . '/../../views/index-login/htmls/actualizar_usuario.php');
        exit; // Se detiene el script porque ya se mostro la página de actualizar.

    } catch (Exception $e) {
        // Si ocurre un error de base de datos al buscar al usuario, se guarda el mensaje de error.
        $_SESSION['error'] = $e->getMessage();
        header("Location:" . $url_retorno); // Y se redirige.
        exit;
    }
}

// MANEJAR LA SOLICITUD POST (CUANDO EL USUARIO ENVÍA EL FORMULARIO)

// Se comprueba si la página fue solicitada usando el método POST.
// Cuando el usuario llena el formulario y hace clic en el botón "Actualizar".
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // CAPA DE VALIDACIÓN DEL LADO DEL SERVIDOR
    // Se valida que el teléfono no esté vacío y que solo contenga dígitos.
    // 'trim()' quita espacios en blanco al inicio y al final.
    $numero_telefono = trim($_POST['numero_telefono'] ?? '');
    if (empty($numero_telefono) || !ctype_digit($numero_telefono)) {
        $_SESSION['error'] = 'El número de teléfono es obligatorio y solo debe contener números.';
        // Si la validación falla, se redirige de vuelta al formulario de actualización para que el usuario corrija el error.
        header("Location: /GericareConnect/controllers/index-login/actualizar_controller.php?id=" . $_POST['id_usuario']);
        exit;
    }
    
    // LIMPIAR CAMPOS SEGÚN EL ROL
    // Se recogen los datos del formulario (los dependientes dle rol).
    $rol = $_POST['rol'] ?? '';
    $fecha_nacimiento = $_POST['fecha_nacimiento'] ?? null;
    $fecha_contratacion = $_POST['fecha_contratacion'] ?? null;
    $tipo_contrato = $_POST['tipo_contrato'] ?? null;
    $contacto_emergencia = $_POST['contacto_emergencia'] ?? null;
    $parentesco = $_POST['parentesco'] ?? null;

    // Si el rol es 'Familiar', los campos de empleado se ponen en 'null' para que no se guarden en la BD.
    if ($rol === 'Familiar') {
        $fecha_nacimiento = null;
        $fecha_contratacion = null;
        $tipo_contrato = null;
        $contacto_emergencia = null;
    } else if ($rol === 'Administrador' || $rol === 'Cuidador'){ // Si no es Familiar (es Admin o Cuidador), el campo de parentesco se pone en 'null'.
        $parentesco = null;
    }

    // Se construye el array '$datos' con toda la información lista para ser enviada al modelo.
    $datos = [
        'id_usuario'               => $_POST['id_usuario'],
        'tipo_documento'           => $_POST['tipo_documento'],
        'documento_identificacion' => $_POST['documento_identificacion'],
        'nombre'                   => $_POST['nombre'],
        'apellido'                 => $_POST['apellido'],
        'direccion'                => $_POST['direccion'],
        'correo_electronico'       => $_POST['correo_electronico'],
        'numero_telefono'          => $numero_telefono, // Se usa la variable ya validada.
        'fecha_contratacion'       => $fecha_contratacion,
        'tipo_contrato'            => $tipo_contrato,
        'contacto_emergencia'      => $contacto_emergencia,
        'fecha_nacimiento'         => $fecha_nacimiento,
        'parentesco'               => $parentesco,
        'nombre_rol'               => $rol,
    ];

    try {
        // Se llama al método 'Actualizar' del modelo pasándole todos los datos.
        $usuario->Actualizar($datos);
        // Si la actualización es exitosa se guarda un mensaje de éxito en la sesión.
        $_SESSION['mensaje'] = "Usuario actualizado correctamente.";
    } catch (Exception $e) {
        // Si ocurre un error de la base de datos (ej: correo duplicado) se guarda el mensaje de error.
        $_SESSION['error'] = $e->getMessage();
    }

    // REDIRECCIÓN AL FINAL DE LA ACTUALIZACIÓN
    // Se define a dónde se va a redirigir al usuario después de la actualización.
    $url_redireccion = '../../views/index-login/htmls/index.php'; // URL por defecto si algo falla.
    
    if (isset($_SESSION['nombre_rol'])) {
        switch ($_SESSION['nombre_rol']) {
            case 'Administrador':
                $url_redireccion = '../../views/admin/html_admin/admin_pacientes.php';
                break;
            case 'Cuidador':
                $url_redireccion = '../../views/cuidador/html_cuidador/cuidadores_panel_principal.php';
                break;
            case 'Familiar':
                $url_redireccion = '../../views/familiar/html_familiar/familiares.php';
                break;
        }
    }
    
    // Se redirige al usuario a la página que le corresponde.
    header("Location: " . $url_redireccion);
    exit;
}
?>
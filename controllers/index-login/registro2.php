<?php
require 'database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombres = htmlspecialchars(trim($_POST['nombres']));
    $apellidos = htmlspecialchars(trim($_POST['apellidos']));
    $correo = htmlspecialchars(trim($_POST['correo']));
    $tipo_documento = htmlspecialchars(trim($_POST['tipo_documento']));
    $documento = htmlspecialchars(trim($_POST['documento']));
    $rol_nombre = htmlspecialchars(trim($_POST['rol']));
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    $error_url = "index.html?error=";

    if (empty($nombres) || empty($apellidos) || empty($correo) || empty($tipo_documento) ||
        empty($documento) || empty($rol_nombre) || empty($password) || empty($confirm_password)) {
        header("Location: " . $error_url . urlencode("Por favor, completa todos los campos."));
        exit();
    }

    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
         header("Location: " . $error_url . urlencode("El formato del correo no es válido."));
        exit();
    }

    if (strlen($password) < 6) {
        header("Location: " . $error_url . urlencode("La contraseña debe tener al menos 6 caracteres."));
        exit();
    }

    if ($password !== $confirm_password) {
        header("Location: " . $error_url . urlencode("Las contraseñas no coinciden."));
        exit();
    }

    $stmt_check_correo = $conn->prepare("SELECT id FROM usuarios WHERE correo = ?");
    $stmt_check_correo->bind_param("s", $correo);
    $stmt_check_correo->execute();
    $stmt_check_correo->store_result();

    if ($stmt_check_correo->num_rows > 0) {
        $stmt_check_correo->close();
        header("Location: " . $error_url . urlencode("Este correo ya está registrado."));
        exit();
    }
    $stmt_check_correo->close();

    $stmt_check_documento = $conn->prepare("SELECT id FROM usuarios WHERE documento = ?");
    $stmt_check_documento->bind_param("s", $documento);
    $stmt_check_documento->execute();
    $stmt_check_documento->store_result();

     if ($stmt_check_documento->num_rows > 0) {
        $stmt_check_documento->close();
        header("Location: " . $error_url . urlencode("Este documento ya está registrado."));
        exit();
    }
    $stmt_check_documento->close();

    $stmt_rol = $conn->prepare("SELECT id FROM roles WHERE nombre = ?");
    $stmt_rol->bind_param("s", $rol_nombre);
    $stmt_rol->execute();
    $result_rol = $stmt_rol->get_result();

    if ($result_rol->num_rows === 1) {
        $rol_row = $result_rol->fetch_assoc();
        $rol_id = $rol_row['id'];
    } else {
        $stmt_rol->close();
        header("Location: " . $error_url . urlencode("Rol seleccionado no válido."));
        exit();
    }
    $stmt_rol->close();

    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    $stmt_insert = $conn->prepare("INSERT INTO usuarios (nombres, apellidos, correo, tipo_documento, documento, rol_id, password) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt_insert->bind_param("sssssis", $nombres, $apellidos, $correo, $tipo_documento, $documento, $rol_id, $hashed_password);

    if ($stmt_insert->execute()) {
        $stmt_insert->close();
        $conn->close();
        header("Location: login.html?success=" . urlencode("Registro exitoso. Ahora puedes iniciar sesión."));
        exit();
    } else {
        $error_db = $stmt_insert->error;
        $stmt_insert->close();
        $conn->close();
        error_log("Error DB Registro: " . $error_db);
        header("Location: " . $error_url . urlencode("Error en el registro. Inténtalo de nuevo más tarde."));
        exit();
    }
} else {
     header("Location: index.html");
     exit();
}
?>
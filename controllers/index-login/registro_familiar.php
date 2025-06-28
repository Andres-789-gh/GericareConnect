<?php
require 'database.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.html");
        exit();
    }

    $familiar_id = $_SESSION['user_id']; 
    $documento_paciente = htmlspecialchars(trim($_POST['documento_paciente']));

    if (empty($documento_paciente)) {
        header("Location: registrar_paciente.html?error=Por favor, ingresa el documento del paciente.");
        exit();
    }


    $stmt_paciente = $conn->prepare("SELECT id FROM usuarios WHERE documento = ? AND rol_id = (SELECT id FROM roles WHERE nombre = 'Paciente')");
    $stmt_paciente->bind_param("s", $documento_paciente);
    $stmt_paciente->execute();
    $result_paciente = $stmt_paciente->get_result();

    if ($result_paciente->num_rows === 0) {
        header("Location: registrar_paciente.html?error=No se encontró ningún paciente
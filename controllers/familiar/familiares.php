<?php
require 'database.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$familiar_id = $_SESSION['user_id'];
$familiares = [];

$stmt = $conn->prepare("SELECT u.nombres, u.apellidos
                         FROM familiares_pacientes fp
                         JOIN usuarios u ON fp.paciente_id = u.id
                         WHERE fp.familiar_id = ?");
$stmt->bind_param("i", $familiar_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $familiares[] = $row;
    }
}

$stmt->close();
$conn->close();

header('Content-Type: application/json');
echo json_encode($familiares);
?>
<?php
require 'database.php';

$pacientes = [];

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['buscar-paciente'])) {
    $buscar = htmlspecialchars(trim($_GET['buscar-paciente']));
    $stmt = $conn->prepare("SELECT nombres, apellidos, documento FROM usuarios WHERE rol_id = (SELECT id FROM roles WHERE nombre = 'Paciente') AND (nombres LIKE ? OR apellidos LIKE ? OR documento LIKE ?)");
    $param = "%" . $buscar . "%";
    $stmt->bind_param("sss", $param, $param, $param);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $pacientes[] = $row;
        }
    }
    $stmt->close();
} else {
    $stmt = $conn->prepare("SELECT nombres, apellidos, documento FROM usuarios WHERE rol_id = (SELECT id FROM roles WHERE nombre = 'Paciente')");
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $pacientes[] = $row;
        }
    }
    $stmt->close();
}

$conn->close();

if (!empty($pacientes)) {
    foreach ($pacientes as $paciente) {
        echo '<li class="paciente-item animated fadeInUp">' . htmlspecialchars($paciente['nombres'] . ' ' . $paciente['apellidos']) . ' - ' . htmlspecialchars($paciente['documento']) . ' <span class="menu-icon"><i class="fas fa-bars"></i></span></li>';
    }
} else {
    echo '<li class="paciente-item">No se encontraron pacientes.</li>';
}
?>
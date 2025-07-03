<?php
// Usamos la conexión a la base de datos que ya tienes
require_once(__DIR__ . '/models/data_base/database.php');

$documento_admin = '1001';
$clave_admin = 'admin123';
// Ciframos la contraseña de la manera correcta que PHP espera
$clave_cifrada = password_hash($clave_admin, PASSWORD_DEFAULT);

echo "<h1>Reparando contraseña del Admin...</h1>";

try {
    $database = new Database();
    $conn = $database->conectar();

    // Actualizamos la contraseña en la base de datos
    $stmt = $conn->prepare("UPDATE tb_usuario SET contraseña = ? WHERE documento_identificacion = ?");
    $stmt->execute([$clave_cifrada, $documento_admin]);

    if ($stmt->rowCount() > 0) {
        echo "<p style='color:green; font-weight:bold;'>¡LISTO! Contraseña reparada.</p>";
        echo "<p>Ya puedes borrar este archivo ('reparar_admin.php') e iniciar sesión.</p>";
    } else {
        echo "<p style='color:red; font-weight:bold;'>ERROR:</p>";
        echo "<p>No se encontró al usuario con documento <strong>$documento_admin</strong>.</p>";
    }

} catch (Exception $e) {
    echo "<p style='color:red; font-weight:bold;'>¡ERROR DE CONEXIÓN!</p>";
    echo "<p>" . $e->getMessage() . "</p>";
}
?>
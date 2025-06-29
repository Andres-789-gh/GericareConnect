<?php
require_once(__DIR__ . '/models/data_base/database.php'); // Ajusta si la ruta es distinta
//localhost/GericareConnect/hola.php
// Arreglo de usuarios con sus nuevas contraseñas temporales
$usuarios = [
    [
        'documento' => 1001,
        'clave_plana' => 'admin123'
    ],
    [
        'documento' => 1002,
        'clave_plana' => 'cuidador123'
    ],
    [
        'documento' => 1003,
        'clave_plana' => 'familiar123'
    ]
];

foreach ($usuarios as $usuario) {
    $hash = password_hash($usuario['clave_plana'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("UPDATE tb_usuario SET contraseña = ? WHERE documento_identificacion = ?");
    $stmt->execute([$hash, $usuario['documento']]);

    echo "Contraseña para documento {$usuario['documento']} actualizada a: {$usuario['clave_plana']}<br>";
}


// git config --global user.name "Edbudy"
// git config --global user.email "giovanni2003go@gmail.com"

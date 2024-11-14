<?php
// Incluir la conexión a la base de datos
include("db.php");

// Configuración de error
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Credenciales de usuario a verificar
$nombre = "admin"; // Cambia esto al nombre del usuario que quieres probar
$password = "admin"; // Cambia esto a la contraseña en texto plano que deseas verificar

// Consulta para obtener la información del usuario en función del nombre
$consulta = "SELECT * FROM usuarios WHERE nombre = ?";
$stmt = $conexion->prepare($consulta);
if (!$stmt) {
    die("Error en la preparación de la consulta: " . $conexion->error);
}
$stmt->bind_param("s", $nombre);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {
    $usuario = $resultado->fetch_assoc();
    echo "Usuario encontrado: " . $usuario['nombre'] . "<br>";

    // Verificar la contraseña
    if (password_verify($password, $usuario['contraseña'])) {
        echo "La contraseña es correcta";
    } else {
        echo "Contraseña incorrecta";
    }
} else {
    echo "Usuario no encontrado";
}

$stmt->close();
$conexion->close();
?>

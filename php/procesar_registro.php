<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Iniciar sesión
session_start();

// Conexión a la base de datos
include("db.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener los datos del formulario
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $cp = $_POST['cp'];
    $estado = $_POST['estado'];
    $contraseña = $_POST['contraseña'];
    
    // Encriptar la contraseña
    $contraseña_hashed = password_hash($contraseña, PASSWORD_DEFAULT);

    // Guardar el código postal en la columna direccion
    $direccion = $cp;

    // Preparar la consulta
    $consulta = "INSERT INTO usuarios (nombre, email, contraseña, direccion, estado) 
                 VALUES (?, ?, ?, ?, ?)";
    $stmt = $conexion->prepare($consulta);

    if ($stmt === false) {
        die("Error en la preparación de la consulta: " . $conexion->error);
    }

    // Usar el estado ingresado por el usuario en lugar de un valor fijo
    $stmt->bind_param("sssss", $nombre, $correo, $contraseña_hashed, $direccion, $estado);
    
    if ($stmt->execute()) {
        echo "<script>
                alert('Registro Exitoso: El usuario ha sido registrado exitosamente.');
                window.location.href = 'login.php';
              </script>";
    } else {
        die("Error al ejecutar la consulta: " . $stmt->error);
    }

    $stmt->close();
}
$conexion->close();
?>

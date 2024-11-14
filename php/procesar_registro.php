<?php
// Procesar el formulario de registro

// Iniciar sesión
session_start();

// Conexión a la base de datos
include("db.php");

// Verificar si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener los datos del formulario
    $nombre = $_POST['nombre'] . ' ' . $_POST['apellidoPaterno'] . ' ' . $_POST['apellidoMaterno'];
    $correo = $_POST['correo'];
    $cp = $_POST['cp'];
    $estado = $_POST['estado'];
    $contraseña = $_POST['contraseña'];
    
    // Encriptar la contraseña
    $contraseña_hashed = password_hash($contraseña, PASSWORD_DEFAULT);

    // Crear la dirección combinando el código postal y el estado
    $direccion = "$cp, $estado";
    $estado_usuario = 'activo'; // Estado predeterminado para un nuevo registro

    // Preparar y ejecutar la consulta
    $consulta = "INSERT INTO usuarios (nombre, email, contraseña, direccion, estado) 
                 VALUES (?, ?, ?, ?, ?)";
    $stmt = $conexion->prepare($consulta);
    if ($stmt === false) {
        echo "<script>
                alert('Error: Hubo un problema en la preparación de la consulta.');
                window.history.back();
              </script>";
        exit;
    }

    $stmt->bind_param("sssss", $nombre, $correo, $contraseña_hashed, $direccion, $estado_usuario);
    
    if ($stmt->execute()) {
        echo "<script>
                alert('Registro Exitoso: El usuario ha sido registrado exitosamente.');
                window.location.href = 'login.php';
              </script>";
    } else {
        echo "<script>
                alert('Error: Hubo un problema al registrar el usuario.');
                window.history.back();
              </script>";
    }

    $stmt->close();
}
$conexion->close();
?>

<?php
// Configuración de la base de datos
$host = 'localhost';        // Nombre del host (generalmente 'localhost')
$usuario = 'root';          // Usuario de la base de datos
$password = '';             // Contraseña del usuario de la base de datos
$baseDeDatos = 'sonrio'; // Nombre de la base de datos

// Crear la conexión
$conexion = new mysqli($host, $usuario, $password, $baseDeDatos);

// Verificar si la conexión es exitosa
if ($conexion->connect_error) {
    die("Error en la conexión: " . $conexion->connect_error);
}
echo "Conexión exitosa";
?>

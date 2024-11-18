<?php
// Datos de conexión
$host = "localhost";
$usuario = "root";
$contraseña = "usbw";
$base_datos = "sonrio";
$puerto = 3306;

// Crear la conexión
$conexion = new mysqli($host, $usuario, $contraseña, $base_datos, $puerto);

// Verificar la conexión
if ($conexion->connect_error) {
    die("Error en la conexión a la base de datos: " . $conexion->connect_error);
}

// Establecer codificación UTF-8
$conexion->set_charset("utf8");
?>

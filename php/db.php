<?php
$conexion = new mysqli("localhost", "root", "usbw", "sonrio"); // Ajusta los datos de conexión
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}
?>

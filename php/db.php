<?php
// Archivo: db.php

$conexion = new mysqli("localhost", "root", "", "sonrio");

if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}
?>

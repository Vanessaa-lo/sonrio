<?php
// login.php

session_start();
$servername = "localhost"; // Cambia esto si es necesario
$username_db = "user"; // Usuario de la base de datos
$password_db = ""; // Contraseña de la base de datos
$dbname = "sonrio"; // Nombre de la base de datos

// Crear la conexión
$conn = new mysqli($servername, $username_db, $password_db, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Obtener los datos del formulario
$username = $_POST['username'];
$password = $_POST['password'];

// Consulta SQL para verificar las credenciales
$sql = "SELECT * FROM usuarios WHERE username='$username' AND password='$password'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Usuario autenticado con éxito
    $_SESSION['username'] = $username;
    echo "Login exitoso. Bienvenido, " . $username;
} else {
    // Credenciales incorrectas
    echo "Usuario o contraseña incorrectos";
}

$conn->close();
?>

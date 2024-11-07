<?php
$servername = "localhost";
$username = "user";
$password = "";
$dbname = "sonrio";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>

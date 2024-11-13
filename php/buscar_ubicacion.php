<?php
include("../db.php");

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

if (isset($_GET['codigo_postal'])) {
    $codigo_postal = $_GET['codigo_postal'];
    $sql = "SELECT estado, pais, direccion FROM ubicaciones WHERE codigo_postal = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $codigo_postal);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode($row);
    } else {
        echo json_encode(['estado' => '', 'pais' => '', 'direccion' => '']);
    }

    $stmt->close();
}
$conn->close();
?>

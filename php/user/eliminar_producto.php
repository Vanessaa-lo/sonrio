<?php
session_start();

$data = json_decode(file_get_contents("php://input"), true);
$idProducto = $data['id'] ?? null;

if ($idProducto !== null && isset($_SESSION['carrito'][$idProducto])) {
    unset($_SESSION['carrito'][$idProducto]);
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error']);
}
?>
    
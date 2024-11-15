<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombreTitular = trim($_POST['nombre-titular']);
    $tarjetaNumero = preg_replace('/\D/', '', $_POST['tarjeta-numero']);
    $tarjetaExpiracion = $_POST['tarjeta-expiracion'];
    $tarjetaCvc = $_POST['tarjeta-cvc'];

    $errores = [];

    // Validación del nombre del titular
    if (empty($nombreTitular) || !preg_match("/^[a-zA-Z\s]+$/", $nombreTitular)) {
        $errores[] = "Nombre del titular inválido.";
    }

    // Validación del número de tarjeta
    if (strlen($tarjetaNumero) !== 16 || !ctype_digit($tarjetaNumero)) {
        $errores[] = "Número de tarjeta inválido.";
    }

    // Validación de la fecha de expiración
    if (!preg_match("/^\d{2}\/\d{2}$/", $tarjetaExpiracion)) {
        $errores[] = "Fecha de expiración inválida.";
    }

    // Validación del CVC
    if (strlen($tarjetaCvc) < 3 || strlen($tarjetaCvc) > 4 || !ctype_digit($tarjetaCvc)) {
        $errores[] = "CVC inválido.";
    }

    // Verificar si existe el total en la sesión
    $totalCarrito = $_SESSION['totalCarrito'] ?? 0;

    if (empty($errores) && $totalCarrito > 0) {
        echo "Pago realizado con éxito. Total: $" . number_format($totalCarrito, 2) . " MXN.";
        
        // Vaciar el carrito y el total después del pago
        unset($_SESSION['carrito']);
        unset($_SESSION['totalCarrito']);
    } else {
        echo "Errores en el pago:<br>" . implode("<br>", $errores);
    }
} else {
    echo "Acceso no permitido.";
}
?>



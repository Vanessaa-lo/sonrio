<?php
session_start();
include("db.php");

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Verificar si el usuario está logueado
if (!isset($_SESSION['id'])) {
    die("Error: El usuario no ha iniciado sesión.");
}

// Obtener el ID del usuario desde la sesión
$usuarioId = $_SESSION['id'];

// Verificar si el carrito tiene productos
$carrito = isset($_SESSION['carrito']) ? $_SESSION['carrito'] : [];
$totalCarrito = 0;

// Variable para los errores
$errores = [];

// Calcular el total del carrito
foreach ($carrito as $producto) {
    $totalCarrito += $producto['precio'] * $producto['cantidad'];
}

// Verificar si agregar $5 de envío (solo si hay productos)
$envio = 5; // Agregar $5 de envío si hay productos

// Calcular el total final
$totalCarrito += $envio;

// Procesar el pago si es un formulario POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombreTitular = trim($_POST['nombre-titular']);
    $tarjetaNumero = preg_replace('/\D/', '', $_POST['tarjeta-numero']);
    $tarjetaExpiracion = $_POST['tarjeta-expiracion'];
    $tarjetaCvc = $_POST['tarjeta-cvc'];

    // Validaciones
    if (empty($nombreTitular) || !preg_match("/^[a-zA-Z\s]+$/", $nombreTitular)) {
        $errores[] = "Nombre del titular inválido.";
    }
    if (strlen($tarjetaNumero) !== 16 || !ctype_digit($tarjetaNumero)) {
        $errores[] = "Número de tarjeta inválido.";
    }
    if (!preg_match("/^\d{2}\/\d{2}$/", $tarjetaExpiracion)) {
        $errores[] = "Fecha de expiración inválida.";
    }
    if (strlen($tarjetaCvc) < 3 || strlen($tarjetaCvc) > 4 || !ctype_digit($tarjetaCvc)) {
        $errores[] = "CVC inválido.";
    }

    if (empty($errores)) {
        // Comprobar si hay productos en el carrito
        if (empty($carrito)) {
            die("No hay productos en el carrito. No se puede realizar el pedido.");
        }

        // Insertar el pedido en la tabla 'pedidos'
        $sqlPedido = "INSERT INTO pedidos (usuario_id, total, estado) VALUES (?, ?, ?)";
        $stmt = $conexion->prepare($sqlPedido);

        if (!$stmt) {
            die("Error en la preparación de la consulta para el pedido: " . $conexion->error);
        }

        $estado = 'pendiente';
        $stmt->bind_param('ids', $usuarioId, $totalCarrito, $estado);
        $stmt->execute();
        $pedidoId = $conexion->insert_id;

        if (!$pedidoId) {
            die("Error al obtener el ID del pedido");
        }

        // Insertar detalles del pedido en la tabla 'pedido_detalles'
        $sqlDetalles = "INSERT INTO pedido_detalles (pedido_id, producto_id, cantidad, precio_unitario) 
                        VALUES (?, ?, ?, ?)";
        $stmtDetalles = $conexion->prepare($sqlDetalles);

        if (!$stmtDetalles) {
            die("Error en la preparación de la consulta para los detalles: " . $conexion->error);
        }

        // Recorrer los productos del carrito para insertar los detalles
        foreach ($carrito as $producto) {
            // Verificar que el producto tenga los valores correctos
            if (isset($producto['id'], $producto['cantidad'], $producto['precio'])) {
                // Insertar cada producto en 'pedido_detalles'
                $stmtDetalles->bind_param('iiid', $pedidoId, $producto['id'], $producto['cantidad'], $producto['precio']);
                $stmtDetalles->execute();
            } else {
                echo "Error: Producto incompleto";
            }
        }

        // Verificar si la inserción de detalles fue exitosa
        if ($stmtDetalles->affected_rows <= 0) {
            die("Error al insertar los detalles del pedido");
        }

        // Insertar el pago en la tabla 'pagos'
        $sqlPago = "INSERT INTO pagos (pedido_id, monto, metodo) VALUES (?, ?, ?)";
        $stmtPago = $conexion->prepare($sqlPago);

        if (!$stmtPago) {
            die("Error en la preparación de la consulta para el pago: " . $conexion->error);
        }

        // El monto es el total final del pedido
        $montoPago = $totalCarrito;
        $metodoPago = 'tarjeta'; // Método de pago fijo

        $stmtPago->bind_param('ids', $pedidoId, $montoPago, $metodoPago);
        $stmtPago->execute();

        if ($stmtPago->affected_rows <= 0) {
            die("Error al insertar el pago");
        }

        // Confirmar que el pedido fue realizado con éxito
        echo "Pedido realizado exitosamente, ID de pedido: " . $pedidoId;

        // Vaciar el carrito
        $_SESSION['carrito'] = [];
    } else {
        echo "Errores: " . implode(", ", $errores);
    }
}
?>

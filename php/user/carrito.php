<?php
session_start();

// Inicializa el carrito si no existe
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

$carrito = $_SESSION['carrito'];

// Maneja la eliminación de un producto
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (isset($input['action'])) {
        if ($input['action'] === 'eliminar' && isset($input['id'])) {
            $idProducto = $input['id'];
            $_SESSION['carrito'] = array_filter($carrito, function ($producto) use ($idProducto) {
                return $producto['id'] !== $idProducto;
            });
            echo json_encode(['status' => 'success']);
            exit;
        } elseif ($input['action'] === 'vaciar') {
            $_SESSION['carrito'] = [];
            echo json_encode(['status' => 'success']);
            exit;
        }
    }
    echo json_encode(['status' => 'error']);
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras | Tienda Sonrio</title>
    <link href="../../estilo/estilos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

<!-- Navbar -->
<div class="navbar">
    <div class="logosonrio">
        <img src="../../estilo/imagenes/logg.png" class="logosonrio" alt="Logo Tienda Kawaii">
    </div>
    <div class="cont-a">
        <a href="home.php"><i class="fas fa-home"></i> Inicio</a>
        <a href="productos.php"><i class="fas fa-box"></i> Productos</a>
        <a href="carrito.php"><i class="fas fa-shopping-cart"></i> Carrito</a>
    </div>
</div>
<!-- Contenedor principal del carrito -->
<div class="cart-container">
            <h1>Carrito de Compras</h1>

            <?php if (empty($carrito)) : ?>
                <div id="empty-cart-message">
                    <p>Tu carrito está vacío. <a href="productos.php">¡Explora nuestros productos!</a></p> <!-- Ajuste en la ruta del enlace -->
                </div>
            <?php else : ?>
                <div class="cart-items">
                    <?php
                    $totalCarrito = 0;
                    foreach ($carrito as $producto) :
                        $nombre = $producto['nombre'] ?? 'Producto desconocido';
                        $precio = $producto['precio'] ?? 0;
                        $cantidad = $producto['cantidad'] ?? 1;
                        $imagen = $producto['imagen'] ?? '../../estilo/imagenes/default.png'; // Ruta ajustada para imagen por defecto

                        $totalProducto = $precio * $cantidad;
                        $totalCarrito += $totalProducto;
                    ?>
                    <div class="cart-item" data-id="<?php echo $producto['id']; ?>">
                        <img src="<?php echo htmlspecialchars($imagen); ?>" alt="<?php echo htmlspecialchars($nombre); ?>" class="item-image">
                        <div class="item-details">
                            <h3><?php echo htmlspecialchars($nombre); ?></h3>
                            <p class="item-price">$<?php echo number_format($precio, 2); ?> MXN</p>
                        </div>
                        <div class="item-quantity">
                            <button class="decrease">-</button>
                            <input type="number" value="<?php echo $cantidad; ?>" min="1" class="quantity-input" onchange="updateTotals()">
                            <button class="increase">+</button>
                        </div>
                        <p class="item-total">$<?php echo number_format($totalProducto, 2); ?> MXN</p>
                        <button class="remove-item" onclick="eliminarProducto('<?php echo $producto['id']; ?>')">X</button>
                    </div>
                    <?php endforeach; ?>
                </div>

        <div class="cart-summary">
            <p><strong>Subtotal:</strong> $<span id="subtotal"><?php echo number_format($totalCarrito, 2); ?></span> MXN</p>
            <p><strong>Envío:</strong> $5.00 MXN</p>
            <p><strong>Total:</strong> $<span id="total"><?php echo number_format($totalCarrito + 5, 2); ?></span> MXN</p>
            <button class="checkout-btn" onclick="window.location.href='../user/pago.php'">Comprar</button>
            <button class="remove-item" onclick="vaciarCarrito()">Vaciar Carrito</button>
        </div>
    <?php endif; ?>

</div>

<footer>
    © 2024 Tienda Sonrio - Todos los derechos reservados
</footer>

<script>
    function eliminarProducto(idProducto) {
        fetch("", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ action: "eliminar", id: idProducto })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                location.reload();
            } else {
                alert("Error al eliminar el producto.");
            }
        });
    }

    function vaciarCarrito() {
        if (confirm("¿Estás seguro de que deseas vaciar el carrito?")) {
            fetch("", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({ action: "vaciar" })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    location.reload();
                } else {
                    alert("Error al vaciar el carrito.");
                }
            });
        }
    }
</script>
</body>
</html>

<?php
session_start();

// Verifica que haya productos en el carrito
$carrito = isset($_SESSION['carrito']) ? $_SESSION['carrito'] : [];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras | Tienda Kawaii</title>
    <link href="../estilo/estilos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

    <!-- Navbar -->
    <div class="navbar">
        <div class="logosonrio">
            <img src="../estilo/imagenes/logg.png" class="logosonrio" alt="Logo Tienda Kawaii">
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
                <p>Tu carrito está vacío. <a href="productos.php">¡Explora nuestros productos!</a></p>
            </div>
        <?php else : ?>
            <div class="cart-items">
                <?php
                $totalCarrito = 0;
                foreach ($carrito as $producto) :
                    $nombre = $producto['nombre'] ?? 'Producto desconocido';
                    $precio = $producto['precio'] ?? 0;
                    $cantidad = $producto['cantidad'] ?? 1;
                    $imagen = $producto['imagen'] ?? 'ruta/a/imagen/por_defecto.png';

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
                    <button class="remove-item" onclick="eliminarProducto('<?php echo $producto['id']; ?>')">Eliminar</button>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="cart-summary">
                <p><strong>Subtotal:</strong> $<span id="subtotal"><?php echo number_format($totalCarrito, 2); ?></span> MXN</p>
                <p><strong>Envío:</strong> $5.00 MXN</p>
                <p><strong>Total:</strong> $<span id="total"><?php echo number_format($totalCarrito + 5, 2); ?></span> MXN</p>
                <button class="checkout-btn" onclick="window.location.href='pago.html'">Proceder al Pago</button>
                <button class="checkout-btn" onclick="vaciarCarrito()">Vaciar Carrito</button>
            </div>
        <?php endif; ?>

    </div>

    <footer>
        © 2024 Tienda Sonrio - Todos los derechos reservados
    </footer>

    <script>
        document.querySelectorAll('.increase').forEach(button => {
            button.addEventListener('click', function() {
                let input = this.previousElementSibling;
                input.value = parseInt(input.value) + 1;
                updateTotals();
            });
        });

        document.querySelectorAll('.decrease').forEach(button => {
            button.addEventListener('click', function() {
                let input = this.nextElementSibling;
                if (input.value > 1) {
                    input.value = parseInt(input.value) - 1;
                    updateTotals();
                }
            });
        });

        function updateTotals() {
            let subtotal = 0;
            document.querySelectorAll('.cart-item').forEach(item => {
                let price = parseFloat(item.querySelector('.item-price').textContent.replace('$', '').replace('MXN', ''));
                let quantity = parseInt(item.querySelector('.quantity-input').value);
                let itemTotal = price * quantity;

                item.querySelector('.item-total').textContent = '$' + itemTotal.toFixed(2) + ' MXN';
                subtotal += itemTotal;
            });

            document.getElementById('subtotal').textContent = subtotal.toFixed(2);
            document.getElementById('total').textContent = (subtotal + 5).toFixed(2);  // Asume un costo de envío de $5
        }

        function vaciarCarrito() {
            if (confirm("¿Estás seguro de que deseas vaciar el carrito?")) {
                fetch("vaciar_carrito.php", {
                    method: "POST"
                }).then(() => {
                    location.reload();
                });
            }
        }

        function eliminarProducto(idProducto) {
            fetch("eliminar_producto.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({ id: idProducto })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    location.reload(); // Refresca la página si se eliminó correctamente
                } else {
                    console.error("Error al eliminar el producto:", data);
                    alert("No se pudo eliminar el producto del carrito.");
                }
            })
            .catch(error => {
                console.error("Error de red:", error);
                alert("Error al conectar con el servidor.");
            });
        }
    </script>
</body>
</html>

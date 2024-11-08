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

        <!-- Título del carrito -->
        <h1>Carrito de Compras</h1>
        
            <!-- Mensaje de carrito vacío -->
            <div id="empty-cart-message" style="display: none;">
                <p>Tu carrito está vacío. <a href="productos.html">¡Explora nuestros productos!</a></p>
            </div>

        <!-- Lista de productos -->
        <div class="cart-items">
            <!-- Producto 1 -->
            <div class="cart-item">
                <img src="..\estilo\imagenes\pngwing.com (1).png" alt="Producto 1" class="item-image">
                <div class="item-details">
                    <h3>Producto 1</h3>
                    <p class="item-price">$10.00</p>
                </div>
                <div class="item-quantity">
                    <button class="decrease">-</button>
                    <input type="number" value="1" min="1" class="quantity-input">
                    <button class="increase">+</button>
                </div>
                <button class="remove-item">Eliminar</button>
            </div>

            <!-- Producto 2 -->
            <div class="cart-item">
                <img src="..\estilo\imagenes\pngwing.com (2).png" alt="Producto 2" class="item-image">
                <div class="item-details">
                    <h3>Producto 2</h3>
                    <p class="item-price">$15.00</p>
                </div>
                <div class="item-quantity">
                    <button class="decrease">-</button>
                    <input type="number" value="1" min="1" class="quantity-input">
                    <button class="increase">+</button>
                </div>
                <button class="remove-item">Eliminar</button>
            </div>

            <!-- Producto 3 -->
            <div class="cart-item">
                <img src="..\estilo\imagenes\pngwing.com.png" alt="Producto 3" class="item-image">
                <div class="item-details">
                    <h3>Producto 3</h3>
                    <p class="item-price">$20.00</p>
                </div>
                <div class="item-quantity">
                    <button class="decrease">-</button>
                    <input type="number" value="1" min="1" class="quantity-input">
                    <button class="increase">+</button>
                </div>
                <button class="remove-item">Eliminar</button>
            </div>
            <div class="cart-item">
                <img src="..\estilo\imagenes\pngwing.com (5).png" alt="Producto 4" class="item-image">
                <div class="item-details">
                    <h3>Producto 4</h3>
                    <p class="item-price">$15.00</p>
                </div>
                <div class="item-quantity">
                    <button class="decrease">-</button>
                    <input type="number" value="1" min="1" class="quantity-input">
                    <button class="increase">+</button>
                </div>
                <button class="remove-item">Eliminar</button>
            </div>

            <!-- Más productos pueden ir aquí -->
        </div>

        <!-- Resumen de la compra -->
        <div class="cart-summary">
            <p><strong>Subtotal:</strong> $60.00</p>
            <p><strong>Envío:</strong> $5.00</p>
            <p><strong>Total:</strong> $65.00</p>
            <a href="pago.html">
                <button class="checkout-btn">Proceder al Pago</button>
            </a>
        </div>

    </div>

    <!-- Footer -->
    <footer>
        © 2024 Tienda Sonrio - Todos los derechos reservados
    </footer>

    <script src="script.js"></script>

</body>

</html>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const cartItemsContainer = document.querySelector('.cart-items');
        const emptyCartMessage = document.getElementById('empty-cart-message');
        const cartSummary = document.querySelector('.cart-summary');

        // Función para verificar si el carrito está vacío y mostrar/ocultar el mensaje
        function checkCartEmpty() {
            if (cartItemsContainer.children.length === 0) {
                emptyCartMessage.style.display = 'block'; // Muestra el mensaje de carrito vacío
                cartSummary.style.display = 'none';       // Oculta el resumen de la compra
            } else {
                emptyCartMessage.style.display = 'none';  // Oculta el mensaje de carrito vacío
                cartSummary.style.display = 'block';      // Muestra el resumen de la compra
            }
        }

        // Función para actualizar el carrito (subtotales, totales, y verificación de carrito vacío)
        function updateCart() {
            let subtotal = 0;

            // Calcula el subtotal sumando el precio por cantidad de cada producto
            document.querySelectorAll('.cart-item').forEach(item => {
                const price = parseFloat(item.querySelector('.item-price').textContent.replace('$', ''));
                const quantity = parseInt(item.querySelector('.quantity-input').value);
                subtotal += price * quantity;
            });

            // Aquí se actualizaría el resumen con subtotal, envío y total
            // Ejemplo: Actualiza elementos de subtotal y total (personalízalo según tus necesidades)
            document.querySelector('.cart-summary').querySelector('p:nth-of-type(1)').textContent = `Subtotal: $${subtotal.toFixed(2)}`;
            
            // Llama a la función para verificar si el carrito está vacío
            checkCartEmpty();
        }

        // Eventos para incrementar/disminuir cantidad
        document.querySelectorAll('.increase').forEach(button => {
            button.addEventListener('click', (event) => {
                const quantityInput = event.target.previousElementSibling;
                quantityInput.value = parseInt(quantityInput.value) + 1;
                updateCart();
            });
        });

        document.querySelectorAll('.decrease').forEach(button => {
            button.addEventListener('click', (event) => {
                const quantityInput = event.target.nextElementSibling;
                if (quantityInput.value > 1) {
                    quantityInput.value = parseInt(quantityInput.value) - 1;
                    updateCart();
                }
            });
        });

        // Evento para eliminar productos
        document.querySelectorAll('.remove-item').forEach(button => {
            button.addEventListener('click', (event) => {
                event.target.closest('.cart-item').remove();
                updateCart();
            });
        });

        // Verificación inicial al cargar la página
        checkCartEmpty();
    });
</script>

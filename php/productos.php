<?php
session_start();

// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "", "sonrio");
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

// Inicializar el carrito en la sesión si no existe
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// Agregar producto al carrito
if (isset($_POST['id'])) {
    $producto_id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $precio = (float)$_POST['precio'];
    $cantidad = (int)$_POST['cantidad'];

    // Verificar si el producto ya está en el carrito
    $encontrado = false;
    foreach ($_SESSION['carrito'] as &$producto) {
        if ($producto['id'] === $producto_id) {
            $producto['cantidad'] += $cantidad;
            $encontrado = true;
            break;
        }
    }

    // Si no está en el carrito, agregarlo como nuevo producto
    if (!$encontrado) {
        $_SESSION['carrito'][] = [
            'id' => $id,
            'nombre' => $nombre,
            'precio' => $precio,
            'cantidad' => $cantidad
        ];
    }

    exit;
}

// Consultar productos
$consulta = "SELECT * FROM productos";
$resultado = $conexion->query($consulta);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos</title>
    <link href="../estilo/estilos.css" rel="stylesheet">
    <link rel="icon" href="../estilo/imagenes/cinta.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <!-- Navbar -->
    <div class="navbar" id="navbar-productos">
        <div class="logosonrio">
            <img src="../estilo/imagenes/logg.png" class="logosonrio" id="logo-productos">
        </div>
        <div class="cont-a">
            <a href="home.php"><i class="fas fa-home"></i> Inicio</a>
            <a href="productos.php"><i class="fas fa-box"></i> Productos</a>
            <a href="carrito.php"><i class="fas fa-shopping-cart"></i> Carrito</a>
        </div>
        <!-- Buscador -->
        <div class="search-bar">
            <i class="fas fa-search"></i>
            <input type="text" placeholder="Buscar productos...">
        </div>
    </div>

    <div class="container" id="productos">
        <div class="cards">
            <?php
            if ($resultado->num_rows > 0) {
                while ($producto = $resultado->fetch_assoc()) {
                    echo '<div class="product-card">';
                    echo '    <img src="' . $producto['imagen_url'] . '" alt="' . $producto['nombre'] . '">';
                    echo '    <h3>' . $producto['nombre'] . '</h3>';
                    echo '    <p>$' . number_format($producto['precio'], 2) . ' MXN</p>';
                    echo '    <form action="productos.php" method="POST">';
                    echo '        <input type="hidden" name="producto_id" value="' . $producto['id'] . '">';
                    echo '        <input type="hidden" name="nombre" value="' . $producto['nombre'] . '">';
                    echo '        <input type="hidden" name="precio" value="' . $producto['precio'] . '">';
                    echo '        <div class="item-quantity2">';
                    echo '            <button type="button" class="decrease">-</button>';
                    echo '            <input type="number" name="cantidad" value="1" min="1" class="quantity-input">';
                    echo '            <button type="button" class="increase">+</button>';
                    echo '        </div>';
                    echo '        <button type="submit" class="add-to-cart-btn">Agregar al carrito</button>';
                    echo '    </form>';
                    echo '</div>';
                }
            } else {
                echo '<p>No hay productos disponibles.</p>';
            }
            ?>
        </div>
    </div>

    <footer>
        © 2024 Tienda Sonrio - Todos los derechos reservados
    </footer>

    <script>
        // Función para incrementar y decrementar la cantidad
        function increment(button) {
            let quantityInput = button.previousElementSibling;
            quantityInput.value = parseInt(quantityInput.value) + 1;
        }

        function decrement(button) {
            let quantityInput = button.nextElementSibling;
            if (quantityInput.value > 1) {
                quantityInput.value = parseInt(quantityInput.value) - 1;
            }
        }

        // Añadir eventos a los botones de incremento y decremento
        document.querySelectorAll('.increase').forEach(button => {
            button.addEventListener('click', function() {
                increment(this);
            });
        });

        document.querySelectorAll('.decrease').forEach(button => {
            button.addEventListener('click', function() {
                decrement(this);
            });
        });
    </script>
</body>

</html>

<?php $conexion->close(); ?>

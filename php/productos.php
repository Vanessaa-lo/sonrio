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
            // Conexión a la base de datos
            $conexion = new mysqli("localhost", "root", "", "sonrio");

            // Verificar conexión
            if ($conexion->connect_error) {
                die("Conexión fallida: " . $conexion->connect_error);
            }

            // Consultar productos
            $consulta = "SELECT * FROM productos";
            $resultado = $conexion->query($consulta);

            // Mostrar cada producto en una tarjeta
            if ($resultado->num_rows > 0) {
                while ($producto = $resultado->fetch_assoc()) {
                    echo '<div class="product-card">';
                    echo '    <img src="' . $producto['imagen_url'] . '" alt="' . $producto['nombre'] . '">';
                    echo '    <h3>' . $producto['nombre'] . '</h3>';
                    echo '    <p>$' . number_format($producto['precio'], 2) . ' MXN</p>';
                    echo '    <form action="productos.php" method="POST">';
                    echo '        <input type="hidden" name="nombre" value="' . $producto['nombre'] . '">';
                    echo '        <input type="hidden" name="precio" value="' . $producto['precio'] . '">';
                    echo '        <button type="submit">Agregar al carrito</button>';
                    echo '        <div class="item-quantity2">';
                    echo '            <button class="decrease">-</button>';
                    echo '            <input type="number" value="1" min="1" class="quantity-input">';
                    echo '            <button class="increase">+</button>';
                    echo '        </div>';
                    echo '    </form>';
                    echo '</div>';
                }
            } else {
                echo '<p>No hay productos disponibles.</p>';
            }

            // Cerrar conexión
            $conexion->close();
            ?>
        </div>
    </div>

    <footer>
        © 2024 Tienda Sonrio - Todos los derechos reservados
    </footer>

    <script>
        // Función para agregar productos al carrito
        function agregarAlCarrito(nombreProducto, precioProducto) {
            if (typeof(Storage) !== "undefined") {
                let carrito = JSON.parse(localStorage.getItem("carrito")) || [];
                let producto = {
                    nombre: nombreProducto,
                    precio: precioProducto
                };
                carrito.push(producto);
                localStorage.setItem("carrito", JSON.stringify(carrito));
                alert(nombreProducto + " ha sido agregado al carrito.");
            } else {
                alert("Lo siento, tu navegador no soporta almacenamiento local.");
            }
        }
    </script>
</body>

</html>
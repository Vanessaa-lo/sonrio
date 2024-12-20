<?php
session_start();

// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "usbw", "sonrio", 3306);
if ($conexion->connect_error) {
    die("<script>Swal.fire('Error', 'Conexión fallida a la base de datos.', 'error');</script>");
}
$conexion->set_charset("utf8");

// Inicializar el carrito en la sesión si no existe
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// Agregar producto al carrito (solo si la solicitud es AJAX)
if (isset($_POST['producto_id']) && isset($_POST['ajax'])) {
    $producto_id = $_POST['producto_id'];
    $nombre = $_POST['nombre'];
    $precio = (float)$_POST['precio'];
    $cantidad = (int)$_POST['cantidad'];
    $imagen = $_POST['imagen']; // Obtiene la URL de la imagen desde el formulario

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
            'id' => $producto_id,
            'nombre' => $nombre,
            'precio' => $precio,
            'cantidad' => $cantidad,
            'imagen' => $imagen // Agregar la imagen al carrito
        ];
    }

    echo json_encode(["status" => "success"]);
    exit;
}

// Búsqueda de productos
$query = $_POST['query'] ?? '';
$consulta = "SELECT id, nombre, descripcion, precio, url_imagen FROM productos WHERE nombre LIKE ?";
$stmt = $conexion->prepare($consulta);
$searchTerm = '%' . $query . '%';
$stmt->bind_param("s", $searchTerm);
$stmt->execute();
$resultado = $stmt->get_result();

// Si es una solicitud AJAX, solo devolvemos los productos
if (isset($_POST['query'])) {
    if ($resultado->num_rows > 0) {
        while ($producto = $resultado->fetch_assoc()) {
            $imagen_url = !empty($producto['url_imagen']) ? 'data:image/png;base64,' . $producto['url_imagen'] : 'ruta/a/imagen/por_defecto.png';
            $precio = !empty($producto['precio']) ? number_format($producto['precio'], 2) : 'Precio no disponible';
            $descripcion = !empty($producto['descripcion']) ? $producto['descripcion'] : 'Descripción no disponible';

            echo '<div class="product-card">';
            echo '    <h3>' . $producto['nombre'] . '</h3>';
            echo '    <img src="' . $imagen_url . '" alt="' . $producto['nombre'] . '">';
            echo '    <p>$' . $precio . ' MXN</p>';
            echo '    <p>' . $descripcion . '</p>';
            echo '    <form onsubmit="return agregarAlCarrito(event)">';
            echo '        <input type="hidden" name="producto_id" value="' . $producto['id'] . '">';
            echo '        <input type="hidden" name="nombre" value="' . $producto['nombre'] . '">';
            echo '        <input type="hidden" name="precio" value="' . $producto['precio'] . '">';
            echo '        <input type="hidden" name="imagen" value="' . $imagen_url . '">';
            echo '        <div class="item-quantity2">';
            echo '            <button type="button" class="decrease" onclick="decrementarCantidad(this)">-</button>';
            echo '            <input type="number" name="cantidad" value="1" min="1" class="quantity-input">';
            echo '            <button type="button" class="increase" onclick="incrementarCantidad(this)">+</button>';
            echo '        </div>';
            echo '        <button type="submit" class="add-to-cart-btn">Agregar al carrito</button>';
            echo '    </form>';
            echo '</div>';
        }
    } else {
        echo '<p>No hay productos que coincidan con tu búsqueda.</p>';
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos | Tienda Sonrio</title>
    <link href="../../estilo/estilos.css" rel="stylesheet">
    <link rel="icon" href="../../estilo/imagenes/cinta.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <!-- Navbar -->
    <div class="navbar" id="navbar-productos">
        <div class="logosonrio">
            <img src="../../estilo/imagenes/logg.png" class="logosonrio" id="logo-productos">
        </div>
        <div class="cont-a">
            <a href="home.php"><i class="fas fa-home"></i> Inicio</a>
            <a href="productos.php"><i class="fas fa-box"></i> Productos</a>
            <a href="carrito.php"><i class="fas fa-shopping-cart"></i> Carrito</a>
            <a href="../login.php"><i class="fas fa-user"></i> Inicio de Sesión</a>
        </div>
        <!-- Buscador -->
        <div class="search-bar">
            <i class="fas fa-search"></i>
            <input type="text" id="buscarProducto" placeholder="¿Qué estás buscando?" oninput="buscarProductos()">
        </div>
    </div>

    <!-- Contenedor de productos -->
    <div class="container" id="productos">
        <div class="cards">
            <?php
            if ($resultado->num_rows > 0) {
                while ($producto = $resultado->fetch_assoc()) {
                    $imagen_url = !empty($producto['url_imagen']) ? 'data:image/png;base64,' . $producto['url_imagen'] : 'ruta/a/imagen/por_defecto.png';
                    $precio = !empty($producto['precio']) ? number_format($producto['precio'], 2) : 'Precio no disponible';
                    $descripcion = !empty($producto['descripcion']) ? $producto['descripcion'] : 'Descripción no disponible';

                    echo '<div class="product-card">';
                    echo '    <h3>' . $producto['nombre'] . '</h3>';
                    echo '    <img src="' . $imagen_url . '" alt="' . $producto['nombre'] . '">';
                    echo '    <p>$' . $precio . ' MXN</p>';
                    echo '    <p>' . $descripcion . '</p>';
                    echo '    <form onsubmit="return agregarAlCarrito(event)">';
                    echo '        <input type="hidden" name="producto_id" value="' . $producto['id'] . '">';
                    echo '        <input type="hidden" name="nombre" value="' . $producto['nombre'] . '">';
                    echo '        <input type="hidden" name="precio" value="' . $producto['precio'] . '">';
                    echo '        <input type="hidden" name="imagen" value="' . $imagen_url . '">';
                    echo '        <div class="item-quantity2">';
                    echo '            <button type="button" class="decrease" onclick="decrementarCantidad(this)">-</button>';
                    echo '            <input type="number" name="cantidad" value="1" min="1" class="quantity-input">';
                    echo '            <button type="button" class="increase" onclick="incrementarCantidad(this)">+</button>';
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
        function incrementarCantidad(boton) {
            let input = boton.previousElementSibling;
            input.value = parseInt(input.value) + 1;
        }

        function decrementarCantidad(boton) {
            let input = boton.nextElementSibling;
            if (input.value > 1) {
                input.value = parseInt(input.value) - 1;
            }
        }

        function agregarAlCarrito(event) {
            event.preventDefault();
            let form = event.target;
            let formData = new FormData(form);
            formData.append("ajax", "1");

            fetch("productos.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === "success") {
                    Swal.fire({
                        icon: "success",
                        title: "¡Producto agregado!",
                        text: "El producto se ha agregado correctamente al carrito.",
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            })
            .catch(error => console.error("Error:", error));
        }

        function buscarProductos() {
            const query = document.getElementById("buscarProducto").value;

            fetch("productos.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: "query=" + encodeURIComponent(query)
            })
            .then(response => response.text())
            .then(html => {
                document.getElementById("productos").innerHTML = html;
            })
            .catch(error => console.error("Error en la búsqueda:", error));
        }
    </script>
</body>
</html>

<?php $conexion->close(); ?>

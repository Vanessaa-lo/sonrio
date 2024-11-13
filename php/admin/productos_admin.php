<?php
session_start();

// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "", "sonrio");
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

// Obtener los productos
$consulta = "SELECT id, nombre, descripcion, precio, stock, url_imagen FROM productos";
$resultado = $conexion->query($consulta);

// Verificar si hay una solicitud para agregar un nuevo producto
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['nombre']) && isset($_POST['precio'])) {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];
    $imagen = $_FILES['imagen']['tmp_name'];
    
    // Subir imagen si se proporciona
    if ($imagen) {
        $imagen_contenido = addslashes(file_get_contents($imagen));
    } else {
        $imagen_contenido = NULL;  // Si no se proporciona imagen, usar NULL
    }

    $consulta_insert = "INSERT INTO productos (nombre, descripcion, precio, stock, url_imagen) 
                        VALUES ('$nombre', '$descripcion', '$precio', '$stock', '$imagen_contenido')";
    $conexion->query($consulta_insert);
    header("Location: productos.php");  // Redirigir a la misma página para ver el nuevo producto agregado
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../../assets/imagenes/icon.ico" type="image/x-icon">
    <title>Admin</title>
    <link rel="stylesheet" href="../../estilo/admin.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body>

<div class="container">
        <h1>Productos Disponibles</h1>
        <table class="tabla-productos">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Precio</th>
                    <th>Stock</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($resultado->num_rows > 0) {
                    while ($producto = $resultado->fetch_assoc()) {

                        echo '<tr>';
                        echo '<td>' . $producto['nombre'] . '</td>';
                        echo '<td>' . $producto['descripcion'] . '</td>';
                        echo '<td>' . number_format($producto['precio'], 2) . ' MXN</td>';
                        echo '<td>' . $producto['stock'] . '</td>';
                     
                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="5">No hay productos disponibles.</td></tr>';
                }
                ?>
            </tbody>
        </table>

        <div class="agregar-producto">
            <h2>Agregar Nuevo Producto</h2>
            <form action="productos.php" method="POST" enctype="multipart/form-data">
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" required>
                
                <label for="descripcion">Descripción:</label>
                <textarea id="descripcion" name="descripcion" required></textarea>
                
                <label for="precio">Precio:</label>
                <input type="number" id="precio" name="precio" step="0.01" required>
                
                <label for="stock">Stock:</label>
                <input type="number" id="stock" name="stock" required>
                
                <label for="imagen">Imagen:</label>
                <input type="file" id="imagen" name="imagen">
                
                <button type="submit">Agregar Producto</button>
            </form>
        </div>
    </div>

    <footer>
        © 2024 Tienda Sonrio - Todos los derechos reservados
    </footer>

</body>
</html>

<?php
$conexion->close();
?>
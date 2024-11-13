<?php
session_start();

// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "", "sonrio");
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

// Eliminar producto si se recibe el ID por GET
if (isset($_GET['eliminar_id'])) {
    $id_producto = $_GET['eliminar_id'];
    
    // Eliminar producto de la base de datos
    $consulta_eliminar = "DELETE FROM productos WHERE id = ?";
    $stmt = $conexion->prepare($consulta_eliminar);
    $stmt->bind_param("i", $id_producto);
    $stmt->execute();

    // Redirigir de nuevo a la página para mostrar los cambios
    header("Location: productos_admin.php");
    exit();
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
    header("Location: productos_admin.php");  // Redirigir a la misma página para ver el nuevo producto agregado
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
<button class="btn-agregar" onclick="abrirPopUp()">Agregar Nuevo Producto</button>
    <h1>Productos Disponibles</h1>
  

    <table class="tabla-productos">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Precio</th>
                <th>Stock</th>
                <th>Acciones</th>
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
        echo '<td>
                <button class="btn-modificar" onclick="modificarProducto(' . $producto['id'] . ')">Modificar</button>
                <button class="btn-eliminar" onclick="eliminarProducto(' . $producto['id'] . ')">Eliminar</button>
              </td>';
        echo '</tr>';
    }
} else {
    echo '<tr><td colspan="5">No hay productos disponibles.</td></tr>';
}
?>

        </tbody>
    </table>

   <!-- Pop-up para agregar nuevo producto -->
<div id="popup-agregar" class="popup">
    <div class="popup-contenido">
        <span class="popup-cerrar" onclick="cerrarPopUp()">&times;</span>
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
        <!-- Botón para cerrar el pop-up -->
        <button class="btn-cerrar" onclick="cerrarPopUp()">Cerrar</button>
    </div>
</div>


</div>

<script>
    // Funciones para abrir y cerrar el pop-up
    function abrirPopUp() {
        document.getElementById('popup-agregar').style.display = 'block';
    }

    function cerrarPopUp() {
        document.getElementById('popup-agregar').style.display = 'none';
    }

    // Funciones para modificar y eliminar productos
    // Funciones para modificar y eliminar productos
function modificarProducto(id) {
    alert('Modificar producto ID: ' + id);
    // Aquí podrías redirigir a una página de modificación o abrir un formulario similar al pop-up
}

function eliminarProducto(id) {
    if (confirm('¿Estás seguro de eliminar este producto?')) {
        // Redirigir al servidor para eliminar el producto
        window.location.href = 'productos_admin.php?eliminar_id=' + id;
    }
}
// Funciones para abrir y cerrar el pop-up
function abrirPopUp() {
    document.getElementById('popup-agregar').style.display = 'block';
}

function cerrarPopUp() {
    document.getElementById('popup-agregar').style.display = 'none';
}


</script>

</body>
</html>
<?php
$conexion->close();
?>
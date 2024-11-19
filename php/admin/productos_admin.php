<?php
session_start();

$conexion = new mysqli("localhost", "root", "usbw", "sonrio", 3306);
if ($conexion->connect_error) {
    die("<script>Swal.fire('Error', 'Conexión fallida a la base de datos.', 'error');</script>");
}
$conexion->set_charset("utf8");

// Función para eliminar producto si se recibe el ID por GET
if (isset($_GET['eliminar_id'])) {
    $id_producto = $_GET['eliminar_id'];

    // Obtener el nombre del producto antes de eliminarlo
    $consulta_nombre = "SELECT nombre FROM productos WHERE id = ?";
    $stmt_nombre = $conexion->prepare($consulta_nombre);
    $stmt_nombre->bind_param("i", $id_producto);
    $stmt_nombre->execute();
    $resultado_nombre = $stmt_nombre->get_result();
    $nombreProducto = $resultado_nombre->fetch_assoc()['nombre'];

    // Eliminar el producto
    $consulta_eliminar = "DELETE FROM productos WHERE id = ?";
    $stmt_eliminar = $conexion->prepare($consulta_eliminar);
    $stmt_eliminar->bind_param("i", $id_producto);

    if ($stmt_eliminar->execute()) {
        // Registrar la actualización en la tabla de actualizaciones
        $descripcion_actualizacion = "Se eliminó el producto: $nombreProducto";
        $consulta_actualizacion = "INSERT INTO actualizaciones (tipo, descripcion) VALUES ('producto', ?)";
        $stmt_actualizacion = $conexion->prepare($consulta_actualizacion);
        $stmt_actualizacion->bind_param("s", $descripcion_actualizacion);
        $stmt_actualizacion->execute();

        // Respuesta exitosa en JSON
        echo json_encode(['success' => true, 'message' => 'Producto eliminado correctamente.']);
    } else {
        // Error al eliminar el producto
        echo json_encode(['success' => false, 'message' => 'Error al eliminar el producto. Intenta nuevamente.']);
    }
    exit();
}

// Obtener productos existentes
$consulta = "SELECT id, nombre, descripcion, precio, stock, url_imagen FROM productos";
$resultado = $conexion->query($consulta);

// Verificar si se está agregando un nuevo producto
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['nombre']) && isset($_POST['precio'])) {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];
    $imagen_base64 = $_POST['imagen_base64'] ?? NULL;

    // Insertar el nuevo producto en la base de datos
    $consulta_insert = "INSERT INTO productos (nombre, descripcion, precio, stock, url_imagen) 
                        VALUES (?, ?, ?, ?, ?)";
    $stmt_insert = $conexion->prepare($consulta_insert);
    $stmt_insert->bind_param("ssdis", $nombre, $descripcion, $precio, $stock, $imagen_base64);

    if ($stmt_insert->execute()) {
        // Registrar la actualización en la tabla de actualizaciones
        $descripcion_actualizacion = "Se agregó un nuevo producto: $nombre";
        $consulta_actualizacion = "INSERT INTO actualizaciones (tipo, descripcion) VALUES ('producto', ?)";
        $stmt_actualizacion = $conexion->prepare($consulta_actualizacion);
        $stmt_actualizacion->bind_param("s", $descripcion_actualizacion);
        $stmt_actualizacion->execute();

        // Respuesta exitosa en JSON
        echo json_encode(['success' => true, 'message' => 'Producto agregado correctamente.']);
    } else {
        // Error al agregar el producto
        echo json_encode(['success' => false, 'message' => 'Error al agregar el producto. Intenta nuevamente.']);
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
    <link rel="icon" href="../../estilo/imagenes/cinta.png" type="image/x-icon">
    <link rel="stylesheet" href="../../estilo/admin.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<header class="header" id="header-admin">
    <div class="top-bar">
        <button class="btn-salir" onclick="window.location.href='admin.php'">X</button>
        <button class="btn-agregar" onclick="abrirPopUp()">Agregar Nuevo Producto</button>
    </div>
</header>

<div class="container">
    <h1>Productos Disponibles</h1>
    <table class="tabla-productos" id="tabla-productos">
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
            <form id="form-agregar-producto" onsubmit="procesarImagen(event)">
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" required>

                <label for="descripcion">Descripción:</label>
                <textarea id="descripcion" name="descripcion" required></textarea>

                <label for="precio">Precio:</label>
                <input type="number" id="precio" name="precio" step="0.01" required>

                <label for="stock">Stock:</label>
                <input type="number" id="stock" name="stock" required>

                <label for="imagen">Imagen:</label>
                <input type="file" id="imagen" name="imagen" accept="image/*" onchange="convertirImagenBase64()">

                <!-- Campo oculto para almacenar la imagen en base64 -->
                <input type="hidden" id="imagen_base64" name="imagen_base64">

                <button type="submit">Agregar Producto</button>
            </form>
            <button class="btn-cerrar" onclick="cerrarPopUp()">Cerrar</button>
        </div>
    </div>

    <div id="mensaje"></div> <!-- Aquí se mostrará el mensaje de éxito/error -->
</div>

<script>
function abrirPopUp() {
    document.getElementById('popup-agregar').style.display = 'block';
}

function cerrarPopUp() {
    document.getElementById('popup-agregar').style.display = 'none';
}

function eliminarProducto(id) {
    const confirmacion = confirm("¿Estás seguro de eliminar este producto?");
    if (confirmacion) {
        fetch('productos_admin.php?eliminar_id=' + id)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Éxito', data.message, 'success');
                    actualizarTabla();  // Actualiza la tabla después de eliminar
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            });
    }
}

function actualizarTabla() {
    fetch('productos_admin.php') // Recarga los productos
        .then(response => response.text())
}

function convertirImagenBase64() {
    const file = document.getElementById('imagen').files[0];
    const reader = new FileReader();

    reader.onloadend = function() {
        const base64String = reader.result.replace(/^data:image\/[a-z]+;base64,/, "");
        document.getElementById('imagen_base64').value = base64String;
    };

    if (file) {
        reader.readAsDataURL(file);
    }
}

// Proceso del formulario de agregar producto
document.getElementById('form-agregar-producto').addEventListener('submit', function(event) {
    event.preventDefault();
    const formData = new FormData(this);

    fetch('productos_admin.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire('Éxito', data.message, 'success');
            cerrarPopUp();
            actualizarTabla();
        } else {
            Swal.fire('Error', data.message, 'error');
        }
    });
});
</script>
</body>
</html>


<?php
$conexion->close();
?>

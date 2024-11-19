<?php
session_start();

// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "usbw", "sonrio", 3306);
if ($conexion->connect_error) {
    die("<script>Swal.fire('Error', 'Conexión fallida a la base de datos.', 'error');</script>");
}
$conexion->set_charset("utf8");

// Función para eliminar pedido si se recibe el ID por GET
if (isset($_GET['eliminar_id'])) {
    $id_pedido = $_GET['eliminar_id'];

    // Obtener el pedido antes de eliminarlo
    $consulta_nombre = "SELECT id, usuario_id, total FROM pedidos WHERE id = ?";
    $stmt_nombre = $conexion->prepare($consulta_nombre);
    $stmt_nombre->bind_param("i", $id_pedido);
    $stmt_nombre->execute();
    $resultado_nombre = $stmt_nombre->get_result();
    $pedido = $resultado_nombre->fetch_assoc();

    if ($pedido) {
        // Eliminar el pedido
        $consulta_eliminar = "DELETE FROM pedidos WHERE id = ?";
        $stmt_eliminar = $conexion->prepare($consulta_eliminar);
        $stmt_eliminar->bind_param("i", $id_pedido);

        if ($stmt_eliminar->execute()) {
            // Registrar actualización
            $descripcion = "Se eliminó el pedido con ID: {$pedido['id']} del usuario: {$pedido['usuario_id']} por un total de: {$pedido['total']}";
            $consulta_actualizacion = "INSERT INTO actualizaciones (tipo, descripcion) VALUES ('pedido', ?)";
            $stmt_actualizacion = $conexion->prepare($consulta_actualizacion);
            $stmt_actualizacion->bind_param("s", $descripcion);
            $stmt_actualizacion->execute();

            // Notificación de éxito
            echo "<script>
                    alert('El pedido ha sido eliminado correctamente.');
                    window.location.href = 'pedidos_admin.php';
                  </script>";
        } else {
            echo "<script>
                    alert('Error al eliminar el pedido.');
                    window.location.href = 'pedidos_admin.php';
                  </script>";
        }
        $stmt_eliminar->close();
    } else {
        echo "<script>
                alert('El pedido no existe.');
                window.location.href = 'pedidos_admin.php';
              </script>";
    }
    exit();
}

// Obtener los pedidos existentes
$consulta = "SELECT id, usuario_id, carrito_id, fecha_pedido, total, estado, colonia, ciudad, codigo_postal, estado_direccion FROM pedidos";
$resultado = $conexion->query($consulta);

if (!$resultado) {
    die("Error en la consulta SQL: " . $conexion->error);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración de Pedidos</title>
    <link rel="stylesheet" href="../../estilo/admin.css">
    <link rel="icon" href="../../estilo/imagenes/cinta.png" type="image/x-icon">
</head>
<body>
<header class="header" id="header-admin">
    <h1 class="h1-usuario">Pedidos Registrados</h1>
    <div class="top-bar">
        <button class="btn-salir" onclick="window.location.href='admin.php'">
           X
        </button>
    </div>
</header>

<div class="container">
    <table class="tabla-productos">
        <thead>
            <tr>
                <th>ID</th>
                <th>ID Usuario</th>
                <th>ID Carrito</th>
                <th>Fecha Pedido</th>
                <th>Total</th>
                <th>Estado</th>
                <th>Colonia</th>
                <th>Ciudad</th>
                <th>Código Postal</th>
                <th>Estado Dirección</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php
        if ($resultado->num_rows > 0) {
            while ($pedido = $resultado->fetch_assoc()) {
                echo '<tr>';
                echo '<td>' . $pedido['id'] . '</td>';
                echo '<td>' . $pedido['usuario_id'] . '</td>';
                echo '<td>' . $pedido['carrito_id'] . '</td>';
                echo '<td>' . $pedido['fecha_pedido'] . '</td>';
                echo '<td>$' . number_format($pedido['total'], 2) . '</td>';
                echo '<td>' . $pedido['estado'] . '</td>';
                echo '<td>' . $pedido['colonia'] . '</td>';
                echo '<td>' . $pedido['ciudad'] . '</td>';
                echo '<td>' . $pedido['codigo_postal'] . '</td>';
                echo '<td>' . $pedido['estado_direccion'] . '</td>';
                echo '<td><button onclick="eliminarPedido(' . $pedido['id'] . ')">Eliminar</button></td>';
                echo '</tr>';
            }
        } else {
            echo '<tr><td colspan="11">No hay pedidos registrados.</td></tr>';
        }
        ?>
        </tbody>
    </table>
</div>

<script>
// Función para confirmar y eliminar pedido
function eliminarPedido(id) {
    const confirmacion = confirm("¿Estás seguro de eliminar este pedido?");
    if (confirmacion) {
        window.location.href = 'pedidos_admin.php?eliminar_id=' + id;
    }
}
</script>
</body>
</html>

<?php
$conexion->close();
?>

<?php
session_start();

// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "usbw", "sonrio", 3306);
if ($conexion->connect_error) {
    die("<script>Swal.fire('Error', 'Conexión fallida a la base de datos.', 'error');</script>");
}
$conexion->set_charset("utf8");

// Obtener los pedidos existentes
$consulta = "SELECT id, usuario_id, fecha_pedido, total, estado, ciudad, colonia, codigo_postal, calle, numero FROM pedidos";
$resultado = $conexion->query($consulta);

if (!$resultado) {
    die("Error en la consulta SQL: " . $conexion->error);
}

// Procesar la actualización del estado si se envía mediante POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_pedido'], $_POST['nuevo_estado'])) {
    $idPedido = $_POST['id_pedido'];
    $nuevoEstado = $_POST['nuevo_estado'];

    // Actualizar el estado en la base de datos
    $actualizarEstado = "UPDATE pedidos SET estado = ? WHERE id = ?";
    $stmt = $conexion->prepare($actualizarEstado);
    $stmt->bind_param("si", $nuevoEstado, $idPedido);

    if ($stmt->execute()) {
        echo "<script>
            Swal.fire('¡Éxito!', 'El estado del pedido se actualizó correctamente.', 'success')
            .then(() => { window.location.href = 'pedidos_admin.php'; });
        </script>";
    } else {
        echo "<script>
            Swal.fire('Error', 'No se pudo actualizar el estado del pedido.', 'error');
        </script>";
    }
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['nuevo_estado'])) {
    $id = intval($_POST['id']); // Aseguramos que sea un número entero
    $nuevoEstado = $_POST['nuevo_estado'];

    // Actualizamos el estado en la base de datos
    $actualizarEstado = "UPDATE pedidos SET estado = ? WHERE id = ?";
    $stmt = $conexion->prepare($actualizarEstado);
    $stmt->bind_param("si", $nuevoEstado, $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al actualizar el estado en la base de datos.']);
    }
    $stmt->close();
    exit;
}



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
$consulta = "SELECT id, usuario_id, carrito_id, fecha_pedido, total, estado, colonia, ciudad, codigo_postal,calle,numero,estado_direccion FROM pedidos";
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                <th>Fecha Pedido</th>
                <th>Total</th>
                <th>Envio</th>
                <th>Estado</th>
                <th>Ciudad</th>
                <th>Colonia</th>
                <th>C.P</th>
                <th>Calle</th>
                <th>Numero de calle</th>
                <th>Cambios</th>
                <th>Estado del Envio</th>
            </tr>
        </thead>
        <tbody>
        <?php
        if ($resultado->num_rows > 0) {
            while ($pedido = $resultado->fetch_assoc()) {
                echo '<tr>';
                echo '<td>' . $pedido['id'] . '</td>';
                echo '<td>' . $pedido['usuario_id'] . '</td>';
                echo '<td>' . $pedido['fecha_pedido'] . '</td>';
                echo '<td>$' . number_format($pedido['total'], 2) . '</td>';
                echo '<td>' . $pedido['estado'] . '</td>';
                echo '<td>' . $pedido['estado_direccion'] . '</td>';
                echo '<td>' . $pedido['ciudad'] . '</td>';
                echo '<td>' . $pedido['colonia'] . '</td>';
                echo '<td>' . $pedido['codigo_postal'] . '</td>';
                echo '<td>' . $pedido['calle'] . '</td>';
                echo '<td>' . $pedido['numero'] . '</td>';
                echo '<td><button  class="btn-eliminar" onclick="eliminarPedido(' . $pedido['id'] . ')">Eliminar</button></td>';
                echo '<td><button class="btn-modificar" onclick="abrirModificarEstado(' . $pedido['id'] . ', \'' . $pedido['estado'] . '\')">Modificar</button></td>';


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

function abrirModificarEstado(id, estado) {
    Swal.fire({
        title: 'Modificar estado del envío',
        input: 'select',
        inputOptions: {
            'Pendiente de envío': 'Pendiente de envío',
            'Enviado': 'Enviado',
            'Entregado': 'Entregado'
        },
        inputValue: estado, // Usamos el parámetro estado correctamente
        showCancelButton: true,
        confirmButtonText: 'Actualizar',
        cancelButtonText: 'Cancelar',
        preConfirm: (nuevoEstado) => {
            return fetch('pedidos_admin.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `id=${id}&nuevo_estado=${encodeURIComponent(nuevoEstado)}`
            })
            .then(response => response.json()) // Parseamos JSON directamente
            .then(data => {
                if (data.success) {
                    Swal.fire('¡Éxito!', 'El estado fue actualizado correctamente.', 'success')
                        .then(() => location.reload()); // Recargamos la página
                } else {
                    Swal.fire('Error', data.message || 'Hubo un problema al actualizar el estado.', 'error');
                }
            })
            .catch(() => {
                Swal.fire('Error', 'No se pudo actualizar el estado. Intenta de nuevo.', 'error');
            });
        }
    });
}


</script>
</body>
</html>



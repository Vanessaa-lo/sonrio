<?php
session_start();

// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "usbw", "sonrio", 3306);
if ($conexion->connect_error) {
    echo "<script>
            Swal.fire('Error', 'Conexión fallida a la base de datos.', 'error')
            .then(() => { window.location.href = 'error.php'; });
          </script>";
    exit;
}
$conexion->set_charset("utf8");

// Procesar la actualización del estado del pedido
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['estado'])) {
    $idPedido = intval($_POST['id']);
    $nuevoEstado = $_POST['estado'];

    // Validar estado permitido
    $estadosValidos = ['pendiente de envio', 'enviado', 'entregado'];
    if (!in_array($nuevoEstado, $estadosValidos)) {
        echo json_encode(['success' => false, 'error' => 'Estado inválido']);
        exit;
    }

    $query = "UPDATE pedidos SET estado = ? WHERE id = ?";
    $stmt = $conexion->prepare($query);

    if (!$stmt) {
        echo json_encode(['success' => false, 'error' => 'Error al preparar la consulta.']);
        exit;
    }

    $stmt->bind_param('si', $nuevoEstado, $idPedido);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'El estado del pedido se actualizó correctamente.']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Error al ejecutar la consulta.']);
    }

    $stmt->close();
    exit;
}

// Eliminar pedido
if (isset($_GET['eliminar_id'])) {
    $idPedido = filter_var($_GET['eliminar_id'], FILTER_VALIDATE_INT);

    if ($idPedido) {
        $consultaNombre = "SELECT id, usuario_id, total FROM pedidos WHERE id = ?";
        $stmt = $conexion->prepare($consultaNombre);
        $stmt->bind_param("i", $idPedido);
        $stmt->execute();
        $pedido = $stmt->get_result()->fetch_assoc();

        if ($pedido) {
            $consultaEliminar = "DELETE FROM pedidos WHERE id = ?";
            $stmtEliminar = $conexion->prepare($consultaEliminar);
            $stmtEliminar->bind_param("i", $idPedido);

            if ($stmtEliminar->execute()) {
                $descripcion = "Se eliminó el pedido con ID: {$pedido['id']} del usuario: {$pedido['usuario_id']} por un total de: {$pedido['total']}";
                $consultaActualizacion = "INSERT INTO actualizaciones (tipo, descripcion) VALUES ('pedido', ?)";
                $stmtActualizacion = $conexion->prepare($consultaActualizacion);
                $stmtActualizacion->bind_param("s", $descripcion);
                $stmtActualizacion->execute();
                $stmtActualizacion->close();

                echo "<script>
                        Swal.fire('¡Éxito!', 'El pedido ha sido eliminado correctamente.', 'success')
                        .then(() => { window.location.href = 'pedidos_admin.php'; });
                      </script>";
            } else {
                echo "<script>
                        Swal.fire('Error', 'No se pudo eliminar el pedido.', 'error');
                      </script>";
            }
            $stmtEliminar->close();
        } else {
            echo "<script>
                    Swal.fire('Error', 'El pedido no existe.', 'error');
                  </script>";
        }
        $stmt->close();
    }
    exit;
}

// Obtener pedidos existentes
$consulta = "SELECT id, usuario_id, fecha_pedido, total, estado, ciudad, colonia, codigo_postal, calle, numero,estado_direccion FROM pedidos";
$resultado = $conexion->query($consulta);

if (!$resultado) {
    echo "<script>
            Swal.fire('Error', 'Error al obtener pedidos.', 'error');
          </script>";
    exit;
}

// Cerrar conexión
$conexion->close();
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
        echo '<td><button class="btn-modificar" onclick="modificarEstado(' . $pedido['id'] . ', \'' . $pedido['estado'] . '\')">Modificar</button></td>';
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
function modificarEstado(id, estadoActual) {
    const estados = ['pendiente de envio', 'enviado', 'entregado'];

    Swal.fire({
        title: 'Cambiar estado del envío',
        input: 'select',
        inputOptions: estados.reduce((acc, estado) => {
            acc[estado] = estado.charAt(0).toUpperCase() + estado.slice(1);
            return acc;
        }, {}),
        inputValue: estadoActual,
        showCancelButton: true,
        confirmButtonText: 'Guardar',
        cancelButtonText: 'Cancelar',
        inputValidator: (value) => {
            if (!value) {
                return 'Debes seleccionar un estado';
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const nuevoEstado = result.value;

            // Llamar a la función PHP para actualizar el estado
            fetch('pedidos_admin.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `id=${id}&estado=${nuevoEstado}`
            })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    Swal.fire('Actualizado', data.message, 'success');
                    location.reload(); // Refrescar para ver cambios
                } else {
                    Swal.fire('Error', data.error, 'error');
                }
            })
            .catch((error) => {
                console.error('Error:', error);
                Swal.fire('Error', 'No se pudo conectar al servidor.', 'error');
            });
        }
    });
}


</script>
</body>
</html>



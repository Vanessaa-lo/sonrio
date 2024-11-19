<?php
session_start();

$conexion = new mysqli("localhost", "root", "usbw", "sonrio", 3306);
if ($conexion->connect_error) {
    die("<script>Swal.fire('Error', 'Conexión fallida a la base de datos.', 'error');</script>");
}
$conexion->set_charset("utf8");

// Función para eliminar usuario si se recibe el ID por POST
if (isset($_POST['eliminar_id'])) {
    $id_usuario = $_POST['eliminar_id'];

    // Obtener el nombre del usuario antes de eliminarlo
    $consulta_nombre = "SELECT nombre FROM usuarios WHERE id = ?";
    $stmt_nombre = $conexion->prepare($consulta_nombre);
    $stmt_nombre->bind_param("i", $id_usuario);
    $stmt_nombre->execute();
    $resultado_nombre = $stmt_nombre->get_result();

    if ($resultado_nombre->num_rows > 0) {
        $nombreUsuario = $resultado_nombre->fetch_assoc()['nombre'];

        // Eliminar usuario
        $consulta_eliminar = "DELETE FROM usuarios WHERE id = ?";
        $stmt_eliminar = $conexion->prepare($consulta_eliminar);
        $stmt_eliminar->bind_param("i", $id_usuario);

        if ($stmt_eliminar->execute()) {
            // Registrar actualización
            $descripcion = "Se eliminó el usuario: $nombreUsuario";
            $consulta_actualizacion = "INSERT INTO actualizaciones (tipo, descripcion) VALUES ('usuario', ?)";
            $stmt_actualizacion = $conexion->prepare($consulta_actualizacion);
            $stmt_actualizacion->bind_param("s", $descripcion);
            $stmt_actualizacion->execute();

            echo json_encode(["success" => true, "message" => "Usuario eliminado correctamente."]);
        } else {
            echo json_encode(["success" => false, "message" => "Error al eliminar el usuario."]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Usuario no encontrado."]);
    }
    exit();
}

// Obtener usuarios existentes
$consulta = "SELECT id, nombre, email, telefono, direccion, estado FROM usuarios";
$resultado = $conexion->query($consulta);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración de Usuarios</title>
    <link rel="stylesheet" href="../../estilo/admin.css">
    <link rel="icon" href="../../estilo/imagenes/cinta.png" type="image/x-icon">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<header class="header" id="header-admin">
    <h1 class="h1-usuario">Usuarios Registrados</h1>
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
                <th>Nombre</th>
                <th>Email</th>
                <th>Teléfono</th>
                <th>Dirección</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php
        if ($resultado->num_rows > 0) {
            while ($usuario = $resultado->fetch_assoc()) {
                echo '<tr id="usuario-' . $usuario['id'] . '">';
                echo '<td>' . $usuario['id'] . '</td>';
                echo '<td>' . $usuario['nombre'] . '</td>';
                echo '<td>' . $usuario['email'] . '</td>';
                echo '<td>' . $usuario['telefono'] . '</td>';
                echo '<td>' . $usuario['direccion'] . '</td>';
                echo '<td>' . $usuario['estado'] . '</td>';
                echo '<td><button onclick="eliminarUsuario(' . $usuario['id'] . ')">Eliminar</button></td>';
                echo '</tr>';
            }
        } else {
            echo '<tr><td colspan="7">No hay usuarios registrados.</td></tr>';
        }
        ?>
        </tbody>
    </table>
</div>

<script>
function eliminarUsuario(id) {
    Swal.fire({
        title: '¿Estás seguro de que quieres eliminar este usuario? 😰',
        text: "Esta acción no se puede deshacer.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Realizamos la solicitud AJAX
            fetch('usuarios_admin.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `eliminar_id=${id}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire(
                        'Eliminado',
                        data.message,
                        'success'
                    ).then(() => {
                        // Removemos la fila de la tabla sin recargar la página
                        document.getElementById(`usuario-${id}`).remove();
                    });
                } else {
                    Swal.fire('Error', data.message || 'No se pudo eliminar el usuario.', 'error');
                }
            })
            .catch(() => {
                Swal.fire('Error', 'Hubo un problema al eliminar el usuario.', 'error');
            });
        }
    });
}
</script>
</body>
</html>

<?php
$conexion->close();
?>

<?php
session_start();

// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "usbw", "sonrio");
if ($conexion->connect_error) {
    die("<script>alert('Conexión fallida a la base de datos.');</script>");
}

// Función para eliminar usuario si se recibe el ID por GET
if (isset($_GET['eliminar_id'])) {
    $id_usuario = $_GET['eliminar_id'];

    // Obtener el nombre del usuario antes de eliminarlo
    $consulta_nombre = "SELECT nombre FROM usuarios WHERE id = ?";
    $stmt_nombre = $conexion->prepare($consulta_nombre);
    $stmt_nombre->bind_param("i", $id_usuario);
    $stmt_nombre->execute();
    $resultado_nombre = $stmt_nombre->get_result();
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

        // Notificación de éxito
        echo "<script>
                alert('El usuario ha sido eliminado correctamente.');
                window.location.href = 'usuarios_admin.php';
              </script>";
    } else {
        echo "<script>
                alert('Error al eliminar el usuario.');
                window.location.href = 'usuarios_admin.php';
              </script>";
    }
    exit();
}

// Función para agregar usuario si se recibe el formulario
if (isset($_POST['agregar_usuario'])) {
    $nombreUsuario = $_POST['nombre'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];

    // Insertar usuario
    $queryUsuario = "INSERT INTO usuarios (nombre, email, telefono) VALUES (?, ?, ?)";
    $stmt_usuario = $conexion->prepare($queryUsuario);
    $stmt_usuario->bind_param("sss", $nombreUsuario, $email, $telefono);

    if ($stmt_usuario->execute()) {
        // Registrar actualización
        $descripcion = "Se agregó un nuevo usuario: $nombreUsuario";
        $consulta_actualizacion = "INSERT INTO actualizaciones (tipo, descripcion) VALUES ('usuario', ?)";
        $stmt_actualizacion = $conexion->prepare($consulta_actualizacion);
        $stmt_actualizacion->bind_param("s", $descripcion);
        $stmt_actualizacion->execute();

        // Notificación de éxito
        echo "<script>
                alert('Usuario agregado correctamente.');
                window.location.href = 'usuarios_admin.php';
              </script>";
    } else {
        echo "<script>
                alert('Error al agregar el usuario.');
                window.location.href = 'usuarios_admin.php';
              </script>";
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
                echo '<tr>';
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
// Función para confirmar y eliminar usuario
function eliminarUsuario(id) {
    const confirmacion = confirm("¿Estás seguro de eliminar este usuario?");
    if (confirmacion) {
        window.location.href = 'usuarios_admin.php?eliminar_id=' + id;
    }
}
</script>
</body>
</html>

<?php
$conexion->close();
?>

<?php
session_start();

// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "", "sonrio");
if ($conexion->connect_error) {
    die("<script>alert('Conexión fallida a la base de datos.');</script>");
}

// Función para eliminar usuario si se recibe el ID por GET
if (isset($_GET['eliminar_id'])) {
    $id_usuario = $_GET['eliminar_id'];
    $consulta_eliminar = "DELETE FROM usuarios WHERE id = ?";
    $stmt = $conexion->prepare($consulta_eliminar);
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    echo "<script>
            alert('El usuario ha sido eliminado correctamente.');
            window.location.href = 'usuarios_admin.php';
          </script>";
    exit();
}

// Obtener usuarios existentes
$consulta = "SELECT id, nombre, email, direccion, estado FROM usuarios";
$resultado = $conexion->query($consulta);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración de Usuarios</title>
    <link rel="stylesheet" href="../../estilo/admin.css">
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
                echo '<td>' . $usuario['direccion'] . '</td>';
                echo '<td>' . $usuario['estado'] . '</td>';
                echo '<td><button onclick="eliminarUsuario(' . $usuario['id'] . ')">Eliminar</button></td>';
                echo '</tr>';
            }
        } else {
            echo '<tr><td colspan="6">No hay usuarios registrados.</td></tr>';
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

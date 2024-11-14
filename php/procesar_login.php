<?php
session_start();
include("db.php"); // Asegúrate de que este archivo contiene la conexión a la base de datos

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener datos del formulario
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Consulta para verificar si el usuario existe
    $consulta = "SELECT * FROM usuarios WHERE email = ?";
    $stmt = $conexion->prepare($consulta);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $usuario = $resultado->fetch_assoc();

        // Verificar la contraseña
        if (password_verify($password, $usuario['contraseña'])) {
            // Establecer la sesión del usuario
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nombre'] = $usuario['nombre'];
            $_SESSION['usuario_tipo'] = $usuario['tipo_usuario']; // Asegúrate de tener este campo en la tabla

            // Redirigir según el tipo de usuario
            if ($usuario['tipo_usuario'] === 'admin') {
                header("Location: admin/admin.php");
            } else {
                header("Location: user/home.php");
            }
            exit;
        } else {
            // Contraseña incorrecta
            header("Location: login.php?error=1");
            exit;
        }
    } else {
        // Usuario no encontrado
        header("Location: login.php?error=1");
        exit;
    }
}
?>

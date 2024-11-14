<?php
session_start();
include("db.php"); // Asegúrate de que este archivo contiene la conexión a la base de datos

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener datos del formulario
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Consulta para verificar si el usuario existe
    $consulta = "SELECT * FROM usuarios WHERE email = ?";
    $stmt = $conexion->prepare($consulta);

    if (!$stmt) {
        die("Error en la preparación de la consulta: " . $conexion->error);
    }
    
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $usuario = $resultado->fetch_assoc();

        // Verificar la contraseña encriptada
        if (password_verify($password, $usuario['contraseña'])) {
            $_SESSION['usuario_nombre'] = $usuario['nombre'];
            $_SESSION['usuario_id'] = $usuario['id'];

            // Redirigir según el nombre del usuario
            if (strtolower($usuario['nombre']) === 'admin') {
                header("Location: }../admin/admin.php");
            } else {
                header("Location: ../user/home.php");
            }
            exit;
        } else {
            header("Location: login.php?error=1"); // Contraseña incorrecta
            exit;
        }
    } else {
        header("Location: login.php?error=1"); // Usuario no encontrado
        exit;
    }
}
?>

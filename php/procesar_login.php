<?php
session_start();
include("db.php");

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Consulta para verificar si el usuario existe
    $consulta = "SELECT * FROM usuarios WHERE nombre = ?";
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
            // Aquí se guarda el ID del usuario y otros detalles en la sesión
            $_SESSION['nombre'] = $usuario['nombre'];
            $_SESSION['id'] = $usuario['id'];  // Guardar el ID de usuario en la sesión

            // Redirigir según el nombre del usuario
            if (strtolower($usuario['nombre']) === 'admin') {
                header("Location: admin/admin.php");
            } else {
                header("Location: user/home.php");
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

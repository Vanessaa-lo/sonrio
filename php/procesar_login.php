<?php
// Procesar los datos de inicio de sesión
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Definir credenciales de administrador y usuario regular
    $admin_usuario = "admin";
    $admin_contrasena = "1";
    
    $usuario_valido = "user";
    $contrasena_valida = "123";

    // Obtener datos del formulario
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Verificar si las credenciales corresponden a un administrador
    if ($username == $admin_usuario && $password == $admin_contrasena) {
        // Redirigir a la página de administración
        header("Location: admin.php");
        exit;
    }
    // Verificar si las credenciales corresponden a un usuario regular
    elseif ($username == $usuario_valido && $password == $contrasena_valida) {
        // Redirigir a la página de usuario regular
        header("Location: home.php");
        exit;
    } else {
        // Redirigir de nuevo al formulario de inicio de sesión con un mensaje de error
        header("Location: login.php?error=1");
        exit;
    }
}
?>

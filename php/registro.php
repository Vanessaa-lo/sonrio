<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Iniciar sesión
session_start();

// Conexión a la base de datos
include("db.php");

$mensaje = '';  // Variable para almacenar el mensaje de notificación
$registroExitoso = false; // Variable para determinar si el registro fue exitoso

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener los datos del formulario
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $cp = $_POST['cp'];
    $estado = $_POST['estado'];
    $contraseña = $_POST['contraseña'];
    
    // Encriptar la contraseña
    $contraseña_hashed = password_hash($contraseña, PASSWORD_DEFAULT);

    // Guardar el código postal en la columna direccion
    $direccion = $cp;

    // Preparar la consulta
    $consulta = "INSERT INTO usuarios (nombre, email, contraseña, direccion, estado) 
                 VALUES (?, ?, ?, ?, ?)";
    $stmt = $conexion->prepare($consulta);

    if ($stmt === false) {
        die("Error en la preparación de la consulta: " . $conexion->error);
    }

    // Usar el estado ingresado por el usuario en lugar de un valor fijo
    $stmt->bind_param("sssss", $nombre, $correo, $contraseña_hashed, $direccion, $estado);
    
    if ($stmt->execute()) {
        $mensaje = "Registro Exitoso: El usuario ha sido registrado exitosamente.";
        $registroExitoso = true;
    } else {
        $mensaje = "Error: Hubo un problema al registrar el usuario.";
    }

    $stmt->close();
}
$conexion->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../estilo/imagenes/cinta.png" type="image/x-icon">
    <title>Registro</title>
    <link rel="stylesheet" href="../estilo/estilos.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="login-page2">
    <div class="login-wrapper2">
        <div class="login-container2">
            <h2>Regístrate</h2>
            <p>Por favor, ingresa tus datos</p>

            <form action="" method="POST">
                <div class="input-row">
                    <div class="input-group2">
                        <label for="nombre">Nombre:</label>
                        <div class="input-icon2">
                            <i class="fas fa-user"></i>
                            <input type="text" id="nombre" name="nombre" required>
                        </div>
                    </div>
                  
                    <div class="input-group2">
                        <label for="correo">Correo Electrónico:</label>
                        <div class="input-icon2">
                            <i class="fas fa-envelope"></i>
                            <input type="email" id="correo" name="correo" required>
                        </div>
                    </div>
                </div>
                <div class="input-row">
                <div class="input-group2">
                    <label for="cp">Código Postal:</label>
                    <div class="input-icon2">
                        <i class="fas fa-map-pin"></i>
                        <input type="text" id="cp" name="cp" required>
                    </div>
                </div>

                    <div class="input-group2">
                        <label for="estado">Estado:</label>
                        <div class="input-icon2">
                            <i class="fas fa-map-marker-alt"></i>
                            <select id="estado" name="estado" required>
                                <option value="" disabled selected>Selecciona tu estado</option>
                                <option value="Jalisco">Jalisco</option>
                                <option value="Ciudad de México">Ciudad de México</option>
                                <option value="Nuevo León">Nuevo León</option>
                                <!-- Agrega más opciones según sea necesario -->
                            </select>
                        </div>
                    </div>  
            </div>

                <div class="input-group2">
                    <label for="contraseña">Contraseña:</label>
                    <div class="input-icon2">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="contraseña" name="contraseña" required>
                    </div>
                </div>

                <button type="submit" class="btn2">Registrar</button>
                <div class="forgot-password2">
                    <p>¿Ya tienes cuenta?</p>
                    <a href="login.php">Inicia Sesión</a>
                </div>
            </form>
        </div>
    </div>

    <?php if (!empty($mensaje)) : ?>
    <script>
        Swal.fire({
            icon: '<?php echo $registroExitoso ? "success" : "error"; ?>',
            title: '<?php echo $registroExitoso ? "Registro Exitoso" : "Error"; ?>',
            text: '<?php echo $mensaje; ?>',
            confirmButtonText: 'Aceptar'
        }).then((result) => {
            <?php if ($registroExitoso) : ?>
                window.location.href = 'login.php';
            <?php endif; ?>
        });
    </script>
    <?php endif; ?>

</body>
</html>

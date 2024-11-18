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
        // Registrar actualización en la tabla `actualizaciones`
        $descripcion = "Se agregó un nuevo usuario: $nombre";
        $consulta_actualizacion = "INSERT INTO actualizaciones (tipo, descripcion) VALUES ('usuario', ?)";
        $stmt_actualizacion = $conexion->prepare($consulta_actualizacion);
        $stmt_actualizacion->bind_param("s", $descripcion);
        $stmt_actualizacion->execute();
        $stmt_actualizacion->close();

        // Notificación de éxito
        $mensaje = "Registro Exitoso: El usuario ha sido registrado exitosamente.";
        $registroExitoso = true;
    } else {
        // Notificación de error
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
    <style>
        /* Estilo general de la página */
        body.login-page2 {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-image: url("imagenes/fondoreg.jpg");
            background-size: cover;
            background-position: center;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 10px;
        }
        .login-wrapper2 {
            display: flex;
            flex-direction: column;
            max-width: 1000px;
            background-color: #d5c5fa8f;
            border-radius: 20px;
            box-shadow: 0px 10px 20px rgba(0, 0, 0, 0.726);
            padding: 40px;
            position: relative;
            text-align: center;
            border: 5px rgb(255, 255, 255) solid;
        }
        .btn2 {
            background-color: #c166b3;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 10px;
            width: 100%;
            font-size: 1em;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s;
            margin-top: 10px;
        }
        .btn2:hover {
            background-color: #8e44ad;
            transform: scale(1.05);
        }
    </style>
</head>
<body class="login-page2">
    <div class="login-wrapper2">
        <div class="login-container2">
            <h2>Regístrate</h2>
            <p>Por favor, ingresa tus datos</p>

            <form action="" method="POST">
                <div class="input-group2">
                    <label for="nombre">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" required>
                </div>
                <div class="input-group2">
                    <label for="correo">Correo Electrónico:</label>
                    <input type="email" id="correo" name="correo" required>
                </div>
                <div class="input-group2">
                    <label for="cp">Código Postal:</label>
                    <input type="text" id="cp" name="cp" required>
                </div>
                <div class="input-group2">
                    <label for="estado">Estado:</label>
                    <select id="estado" name="estado" required>
                        <option value="" disabled selected>Selecciona tu estado</option>
                        <option value="Jalisco">Jalisco</option>
                        <option value="Ciudad de México">Ciudad de México</option>
                        <option value="Nuevo León">Nuevo León</option>
                    </select>
                </div>
                <div class="input-group2">
                    <label for="contraseña">Contraseña:</label>
                    <input type="password" id="contraseña" name="contraseña" required>
                </div>
                <button type="submit" class="btn2">Registrar</button>
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

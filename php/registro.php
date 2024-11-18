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
    $telefono = $_POST['telefono'];
    $cp = $_POST['cp'];
    $estado = $_POST['estado'];
    $contraseña = $_POST['contraseña'];

    // Verificaciones
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $mensaje = "Por favor, ingresa un correo electrónico válido.";
        $registroExitoso = false;
    } elseif (!preg_match("/^[0-9]{10,15}$/", $telefono)) {
        $mensaje = "El número telefónico debe ser numérico y tener entre 10 y 15 dígitos.";
        $registroExitoso = false;
    } elseif (empty($nombre) || empty($correo) || empty($telefono) || empty($cp) || empty($estado) || empty($contraseña)) {
        $mensaje = "Todos los campos son obligatorios.";
        $registroExitoso = false;
    } else {
        // Si las validaciones son correctas, proceder con el registro
        $contraseña_hashed = password_hash($contraseña, PASSWORD_DEFAULT);
        $direccion = $cp;

        // Preparar la consulta
        $consulta = "INSERT INTO usuarios (nombre, email, telefono, contraseña, direccion, estado) 
                     VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conexion->prepare($consulta);

        if ($stmt === false) {
            $mensaje = "Error en la preparación de la consulta.";
            $registroExitoso = false;
        } else {
            $stmt->bind_param("ssssss", $nombre, $correo, $telefono, $contraseña_hashed, $direccion, $estado);

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
                $mensaje = "Error: Hubo un problema al registrar el usuario.";
                $registroExitoso = false;
            }
            $stmt->close();
        }
    }
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
            color: #c166b3;
        }
        h2 {
            font-size: 40px;
            color: #c166b3;
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
        .form-grid {
            margin-top: 20px;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }
        .input-group2 {
            display: flex;
            flex-direction: column;
        }
        .input-group2 label {
            font-size: 16px;
            font-weight: bold;
            color: white;
            margin-bottom: 8px;
        }
        .input-group2 input, 
        .input-group2 select {
            padding: 10px;
            border: 1px solid #dcdcdc;
            border-radius: 8px;
            font-size: 16px;
            background-color: #fafafa;
            transition: border-color 0.3s, background-color 0.3s;
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
            margin-top: 20px;
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
                <div class="form-grid">
                    <div class="input-group2">
                        <label for="nombre">Nombre:</label>
                        <input type="text" id="nombre" name="nombre" required>
                    </div>
                    <div class="input-group2">
                        <label for="correo">Correo Electrónico:</label>
                        <input type="email" id="correo" name="correo" required>
                    </div>
                    <div class="input-group2">
                        <label for="telefono">Número Telefónico:</label>
                        <input type="text" id="telefono" name="telefono" required>
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
                    <div class="input-group2 full-width">
                        <label for="contraseña">Contraseña:</label>
                        <input type="password" id="contraseña" name="contraseña" required>
                    </div>
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
        });
    </script>
    <?php endif; ?>
</body>
</html>

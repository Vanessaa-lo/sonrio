
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

            <form action="procesar_registro.php" method="POST">

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
                </div>

                <button type="submit" class="btn2">Registrar</button>
                <div class="forgot-password2">
                    <p>¿Ya tienes cuenta?</p>
                    <a href="login.php">Inicia Sesión</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
    <!DOCTYPE html>
    <html lang="es">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" href="../estilo/imagenes/cinta.png" type="image/x-icon">
        <title>Login</title>
        <link rel="stylesheet" href="../estilo/estilos.css">
        <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    </head>

    <body class="login-page light-mode">
        <!-- Botón para alternar entre modo claro y oscuro -->
        <button id="toggle-mode" class="btn-mode">
            <i class="fas fa-sun"></i>
        </button>

        <div class="login-wrapper">
            <div class="login-container">
                <h2>Inicia Sesion</h2>
                <p>Por favor, ingresa tus datos</p>
                <?php
// Mostrar pop-up de error si el inicio de sesión falló
if (isset($_GET['error'])) {
    echo "<script>
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Usuario o contraseña incorrectos.',
            confirmButtonText: 'Aceptar'
        });
    </script>";
}
?>


                <form action="procesar_login.php" method="POST">
                    <div class="input-group">
                        <label for="username">Usuario:</label>
                        <div class="input-icon">
                            <i class="fas fa-user"></i>
                            <input type="text" id="username" name="username" required>
                        </div>
                    </div>
                    <div class="input-group">
                        <label for="password">Contraseña:</label>
                        <div class="input-icon">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="password" name="password" required>
                        </div>
                    </div>
                    <button type="submit" class="btn">Ingresar</button>

                    <div class="forgot-password">
                        <a href="#">Olvidé mi contraseña</a>
                    </div>
                </form>
            </div>
            <div class="login-image">
                <img src="../estilo/imagenes/logoo.png" alt="Imagen decorativa de inicio de sesión">
            </div>
        </div>

        <!-- Script para alternar modo claro/oscuro -->
        <script>
            const toggleButton = document.getElementById('toggle-mode');
            const body = document.body;

            toggleButton.addEventListener('click', () => {
                body.classList.toggle('dark-mode');
                body.classList.toggle('light-mode');
                if (body.classList.contains('dark-mode')) {
                    toggleButton.innerHTML = '<i class="fas fa-moon"></i>';
                } else {
                    toggleButton.innerHTML = '<i class="fas fa-sun"></i>';
                }
            });
        </script>
    </body>

    </html>

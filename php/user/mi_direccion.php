<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dirección de Envío | Tienda Sonrio</title>
    <link href="../../estilo/estilos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <!-- Navbar -->
    <div class="navbar">
        <div class="logosonrio">
            <img src="../../estilo/imagenes/logg.png" class="logosonrio" alt="Logo Tienda Kawaii">
        </div>
        <div class="cont-a">
            <a href="home.php"><i class="fas fa-home"></i> Inicio</a>
            <a href="productos.php"><i class="fas fa-box"></i> Productos</a>
            <a href="carrito.php"><i class="fas fa-shopping-cart"></i> Carrito</a>
        </div>
    </div>

    <!-- Contenedor de Dirección -->
    <div class="container-direccion">
        <h2 class="titulo-direccion">Introduce tu Dirección de Envío</h2>
        <form action="procesar_direccion.php" method="POST" class="form-direccion">
            <div class="direccion-row">
                <label for="calle">Calle:</label>
                <input type="text" id="calle" name="calle" placeholder="Ej. Av. Juárez" required>
            </div>
            <div class="direccion-row">
                <label for="numero">Número Exterior:</label>
                <input type="text" id="numero" name="numero" placeholder="Ej. 1234" required>
            </div>
            <div class="direccion-row">
                <label for="colonia">Colonia:</label>
                <input type="text" id="colonia" name="colonia" placeholder="Ej. Centro" required>
            </div>
            <div class="direccion-row">
                <label for="ciudad">Ciudad:</label>
                <input type="text" id="ciudad" name="ciudad" placeholder="Ej. Guadalajara" required>
            </div>
            <div class="direccion-row">
                <label for="estado">Estado:</label>
                <select id="estado" name="estado" required>
                    <option value="" disabled selected>Selecciona tu estado</option>
                    <option value="Jalisco">Jalisco</option>
                    <option value="Ciudad de México">Ciudad de México</option>
                    <option value="Nuevo León">Nuevo León</option>
                    <!-- Agregar más opciones si es necesario -->
                </select>
            </div>
            <div class="direccion-row">
                <label for="codigo-postal">Código Postal:</label>
                <input type="text" id="codigo-postal" name="codigo_postal" placeholder="Ej. 44100" required>
            </div>
            <button type="submit" class="btn-direccion-enviar">Guardar Dirección</button>
        </form>
    </div>

    <!-- Footer -->
    <footer>
        © 2024 Tienda Sonrio - Todos los derechos reservados
    </footer>
</body>
</html>

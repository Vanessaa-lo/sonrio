<?php
session_start();

// Conexión a la base de datos

$conexion = new mysqli("localhost", "root", "usbw", "sonrio", 3306);

if ($conexion->connect_error) {
    die("<script>Swal.fire('Error', 'Conexión fallida a la base de datos.', 'error');</script>");
}
$conexion->set_charset("utf8");
// Obtener la lista de estados únicos desde la base de datos
$estados = [];
$query = "SELECT DISTINCT estado FROM ubicaciones ORDER BY estado ASC";
$result = $conexion->query($query);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $estados[] = $row['estado'];
    }
}

// Procesar solicitud AJAX si se envía un código postal
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['codigo_postal']) && !isset($_POST['calle'])) {
    $codigo_postal = $_POST['codigo_postal'];

    $stmt = $conexion->prepare("SELECT colonia, ciudad, estado FROM ubicaciones WHERE codigo_postal = ?");
    $stmt->bind_param("s", $codigo_postal);
    $stmt->execute();
    $result = $stmt->get_result();

    $ubicaciones = [];
    while ($row = $result->fetch_assoc()) {
        $ubicaciones[] = $row;
    }

    // Devuelve los resultados como JSON
    echo json_encode($ubicaciones);
    exit;
}

// Procesar la dirección cuando se envía el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['calle'])) {
    $codigoPostal = trim($_POST['codigo_postal']);
    $calle = trim($_POST['calle']);
    $numero = trim($_POST['numero']);
    $colonia = trim($_POST['colonia']);
    $ciudad = trim($_POST['ciudad']);
    $estado = trim($_POST['estado']);

    // Validar que todos los campos estén completos
    if (empty($codigoPostal) || empty($calle) || empty($numero) || empty($colonia) || empty($ciudad) || empty($estado)) {
        die("<script>Swal.fire('Error', 'Todos los campos de dirección son obligatorios.', 'error');</script>");
    }

    // Guardar en la sesión
    $_SESSION['direccion'] = [
        'codigo_postal' => $codigoPostal,
        'calle' => $calle,
        'numero' => $numero,
        'colonia' => $colonia,
        'ciudad' => $ciudad,
        'estado_direccion' => $estado,
    ];

    // Redirigir de nuevo a la página de pago
    header('Location: pago.php');
    exit;
}
?>



<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dirección de Envío | Tienda Sonrio</title>
    <link href="../../estilo/admin.css" rel="stylesheet">
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
        <form method="POST" action="mi_direccion.php" class="form-direccion">
            <div class="direccion-row">
                <label for="codigo-postal">Código Postal:</label>
                <input type="text" id="codigo-postal" name="codigo_postal" placeholder="Ej. 44100" required>
            </div>
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
                    <?php foreach ($estados as $estado): ?>
                        <option value="<?= htmlspecialchars($estado) ?>"><?= htmlspecialchars($estado) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn-direccion-enviar">Guardar Dirección</button>
        </form>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const codigoPostalInput = document.getElementById("codigo-postal");

            codigoPostalInput.addEventListener("blur", function () {
                const codigoPostal = this.value;

                if (codigoPostal) {
                    fetch(window.location.href, { // Hacer la solicitud a la misma página
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'codigo_postal=' + encodeURIComponent(codigoPostal),
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.length > 0) {
                            // Rellenar los campos con la primera coincidencia
                            document.getElementById("colonia").value = data[0].colonia;
                            document.getElementById("ciudad").value = data[0].ciudad;
                            document.getElementById("estado").value = data[0].estado;
                        } else {
                            Swal.fire('Lo sentimos...', 'Aún no tenemos envios para esta zona', 'warning');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire('Error', 'Ocurrió un problema al buscar las ubicaciones.', 'error');
                    });
                }
            });
        });
    </script>

    <!-- Footer -->
    <footer>
        © 2024 Tienda Sonrio - Todos los derechos reservados
    </footer>
</body>
</html>

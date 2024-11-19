<?php
session_start();

// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "usbw", "sonrio", 3306);
if ($conexion->connect_error) {
    die("<script>Swal.fire('Error', 'Conexión fallida a la base de datos.', 'error');</script>");
}
$conexion->set_charset("utf8");

if (!isset($_SESSION['id'])) {
    die("Error: El usuario no ha iniciado sesión.");
}

$usuarioId = $_SESSION['id'];
$carrito = $_SESSION['carrito'] ?? [];
$totalCarrito = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombreTitular = trim($_POST['nombre-titular'] ?? '');
    $tarjetaNumero = preg_replace('/\D/', '', $_POST['tarjeta-numero'] ?? '');
    $tarjetaExpiracion = $_POST['tarjeta-expiracion'] ?? '';
    $tarjetaCvc = $_POST['tarjeta-cvc'] ?? '';

    $errores = [];
    if (empty($nombreTitular) || !preg_match("/^[a-zA-Z\s]+$/", $nombreTitular)) {
        $errores[] = "Nombre del titular inválido.";
    }
    if (strlen($tarjetaNumero) !== 16 || !ctype_digit($tarjetaNumero)) {
        $errores[] = "Número de tarjeta inválido.";
    }
    if (!preg_match("/^\d{2}\/\d{2}$/", $tarjetaExpiracion)) {
        $errores[] = "Fecha de expiración inválida.";
    }
    if (strlen($tarjetaCvc) < 3 || strlen($tarjetaCvc) > 4 || !ctype_digit($tarjetaCvc)) {
        $errores[] = "CVC inválido.";
    }

    if (!empty($errores)) {
        $_SESSION['mensaje'] = ['tipo' => 'error', 'texto' => implode(", ", $errores)];
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }

    if (empty($carrito)) {
        $_SESSION['mensaje'] = ['tipo' => 'error', 'texto' => 'No hay productos en el carrito.'];
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }

    foreach ($carrito as $producto) {
        $totalCarrito += $producto['precio'] * $producto['cantidad'];
    }
    $envio = 5;
    $totalCarrito += $envio;

    $direccion = $_SESSION['direccion'] ?? null;
    if (!$direccion) {
        $_SESSION['mensaje'] = ['tipo' => 'warning', 'texto' => 'Por favor completa tu dirección antes de realizar el pago.'];
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }

    $sqlPedido = "INSERT INTO pedidos (usuario_id, total, estado, codigo_postal, colonia, ciudad, estado_direccion) 
    VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conexion->prepare($sqlPedido);
        if (!$stmt) {
        $_SESSION['mensaje'] = ['tipo' => 'error', 'texto' => 'Error al preparar la consulta: ' . $conexion->error];
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
        }

        // Definir los valores que se insertarán en la tabla
        $estadoPedido = 'pendiente de envio';

        $stmt->bind_param(
        'idsssss',
        $usuarioId,
        $totalCarrito,
        $estadoPedido,
        $direccion['codigo_postal'],
        $direccion['colonia'],
        $direccion['ciudad'],
        $direccion['estado_direccion']
        );

    $stmt->execute();
    $pedidoId = $conexion->insert_id;

    if ($pedidoId) {
        $sqlDetalles = "INSERT INTO pedido_detalles (pedido_id, producto_id, cantidad, precio_unitario) 
                        VALUES (?, ?, ?, ?)";
        $stmtDetalles = $conexion->prepare($sqlDetalles);

        foreach ($carrito as $producto) {
            if (isset($producto['id'], $producto['cantidad'], $producto['precio'])) {
                $stmtDetalles->bind_param('iiid', $pedidoId, $producto['id'], $producto['cantidad'], $producto['precio']);
                $stmtDetalles->execute();
            }
        }

        $sqlPago = "INSERT INTO pagos (pedido_id, monto, metodo) VALUES (?, ?, ?)";
        $stmtPago = $conexion->prepare($sqlPago);
        $metodoPago = 'tarjeta';
        $stmtPago->bind_param('ids', $pedidoId, $totalCarrito, $metodoPago);
        $stmtPago->execute();

        $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => "Pedido realizado exitosamente."];
        $_SESSION['carrito'] = [];
        unset($_SESSION['direccion']);
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    } else {
        $_SESSION['mensaje'] = ['tipo' => 'error', 'texto' => 'No se pudo guardar el pedido.'];
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}
?>




<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pago | Tienda Sonrio</title>
    <link href="../../estilo/estilos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- Agregar SweetAlert2 -->
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
   
</div>
<!-- Script para mostrar SweetAlert -->



    <!-- Sección de Pago -->
    <div class="container-pago">
    <script>
        <?php if (isset($_SESSION['mensaje'])): ?>
            Swal.fire({
                icon: '<?php echo $_SESSION['mensaje']['tipo']; ?>',
                title: '<?php echo ucfirst($_SESSION['mensaje']['tipo']); ?>',
                text: '<?php echo $_SESSION['mensaje']['texto']; ?>',
                showConfirmButton: true
            });
            <?php unset($_SESSION['mensaje']); ?>
        <?php endif; ?>
    </script>
 
    <div class="direccion">
    <button class="btn-direccion" onclick="window.location.href='mi_direccion.php'" >
       Dirección de envio<i class="fas fa-chevron-right"></i>

    </button>
    </div>
        <h2 class="titulo-pago">¡Completa tu Compra!</h2>
        <div class="resumen-pago">
            <h3>Resumen de Compra</h3>
            <ul>
                <?php 
                    // Solo mostrar productos si hay en el carrito
                    if (count($carrito) > 0) {
                        foreach ($carrito as $producto): 
                            $nombre = $producto['nombre'] ?? 'Producto desconocido';
                            $precio = $producto['precio'] ?? 0;
                            $cantidad = $producto['cantidad'] ?? 1;
                            $totalProducto = $precio * $cantidad;
                            $totalCarrito += $totalProducto;
                        ?>
                            <li>
                                <strong><?php echo htmlspecialchars($nombre); ?></strong> 
                                x <?php echo htmlspecialchars($cantidad); ?> - $<?php echo number_format($totalProducto, 2); ?>
                            </li>
                        <?php endforeach; ?>
                    <?php } else { ?>
                        <p>No hay productos en el carrito.</p>
                    <?php } ?>
            </ul>
            <hr>
            <p><strong>Subtotal: $<?php echo number_format($totalCarrito, 2); ?></strong> MXN</p>

            <!-- Mostrar el costo de envío solo si hay productos -->
            <?php if (count($carrito) > 0): ?>
                <p><strong>Envío: $5.00</strong> MXN</p>
            <?php endif; ?>
            
            <hr>
            <p><strong>Total: $<?php echo number_format($totalCarrito + (count($carrito) > 0 ? 5 : 0), 2); ?></strong> MXN</p>
        </div>

        <!-- Formulario de Pago con Tarjeta -->
        <div class="metodo-pago">
            <h3>Introduce tus Datos de Pago</h3>
            <form id="form-pago" method="POST">
                <div class="pago-tarjeta">
                    <label for="nombre-titular">Nombre del Titular</label>
                    <input type="text" id="nombre-titular" name="nombre-titular" placeholder="Nombre del Titular" required aria-label="Nombre del titular de la tarjeta" autocomplete="name">

                    <label for="tarjeta-numero">Número de tarjeta</label>
                    <input type="tel" id="tarjeta-numero" name="tarjeta-numero" placeholder="---- ---- ---- ----" required aria-label="Número de tarjeta" autocomplete="cc-number">

                    <label for="tarjeta-expiracion">Fecha de expiración</label>
                    <input type="tel" id="tarjeta-expiracion" name="tarjeta-expiracion" placeholder="MM/AA" required aria-label="Fecha de expiración" autocomplete="cc-exp">

                    <label for="tarjeta-cvc">CVC</label>
                    <input type="tel" id="tarjeta-cvc" name="tarjeta-cvc" placeholder="---" required aria-label="Código de seguridad" autocomplete="cc-csc" maxlength="4">

                </div>

                <button type="submit" class="boton-pago">Realizar Pago</button>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        © 2024 Tienda Sonrio - Todos los derechos reservados
    </footer>
    <!-- Script para mostrar el modal -->

    <!-- Script para mostrar la ventana emergente con SweetAlert2 -->
    <script>
        <?php if (isset($pagoExitoso) && $pagoExitoso) : ?>
            Swal.fire({
                icon: 'success',
                title: '¡Pago realizado con éxito!',
                text: 'Gracias por tu compra. 😊',
                timer: 3000,
                showConfirmButton: false
            });
        <?php elseif (isset($pagoExitoso) && !$pagoExitoso) : ?>
            Swal.fire({
                icon: 'error',
                title: 'Error en el pago',
                text: 'Hubo un problema con tu pago. Intenta nuevamente.',
                timer: 3000,
                showConfirmButton: false
            });
        <?php endif; ?>
    </script>


</body>
</html>
<!-- Script de validación y formateo de pago -->
<script>
    document.getElementById("form-pago").addEventListener("submit", function(event) {
        // Verificación de que haya productos en el carrito
        const productosEnCarrito = <?php echo count($carrito); ?>;
        if (productosEnCarrito === 0) {
            alert("No hay productos en el carrito. No se puede realizar el pago.");
            event.preventDefault();
            return;
        }

        // Validación del nombre del titular
        const nombreTitular = document.getElementById("nombre-titular").value.trim();
        if (!/^[a-zA-Z\s]+$/.test(nombreTitular) || nombreTitular.length < 3) {
            alert("El nombre del titular solo debe contener letras y espacios, y debe tener al menos 3 caracteres.");
            event.preventDefault();
            return;
        }

        // Validación del número de tarjeta
        const tarjetaNumero = document.getElementById("tarjeta-numero").value.replace(/\s/g, '');
        if (tarjetaNumero.length !== 16) {
            alert("El número de tarjeta debe tener 16 dígitos.");
            event.preventDefault();
            return;
        }

        // Validación de la fecha de expiración
        const tarjetaExpiracion = document.getElementById("tarjeta-expiracion").value;
        const [mes, anio] = tarjetaExpiracion.split('/').map(val => parseInt(val));
        const fechaActual = new Date();
        const anioActual = fechaActual.getFullYear() % 100; // Últimos 2 dígitos del año
        const mesActual = fechaActual.getMonth() + 1; // Mes actual (0-11)

        if (isNaN(mes) || isNaN(anio) || mes < 1 || mes > 12 || (anio < anioActual || (anio === anioActual && mes < mesActual))) {
            alert("La fecha de expiración debe ser válida y estar en el futuro.");
            event.preventDefault();
            return;
        }

        // Validación del CVC
        const tarjetaCvc = document.getElementById("tarjeta-cvc").value;

        // Verificar que el CVC tenga entre 3 y 4 dígitos y solo contenga números
        if (tarjetaCvc.length < 3 || tarjetaCvc.length > 4 || isNaN(tarjetaCvc)) {
            alert("El CVC debe tener entre 3 y 4 dígitos.");
            event.preventDefault();
            return;
        }

        // Limitar la longitud del CVC a 4 dígitos como máximo al ingresar
        document.getElementById("tarjeta-cvc").addEventListener("input", function(event) {
            if (this.value.length > 4) {
                this.value = this.value.slice(0, 4); // Limita a 4 dígitos
            }
        });
    });

    // Formateo del número de tarjeta
    document.getElementById("tarjeta-numero").addEventListener("input", function(event) {
        let valor = event.target.value.replace(/\D/g, '').substring(0, 16);
        event.target.value = valor.match(/.{1,4}/g)?.join(' ') || valor; // Agrega espacio cada 4 dígitos
    });

    // Formateo de la fecha de expiración
    document.getElementById("tarjeta-expiracion").addEventListener("input", function(event) {
        let valor = event.target.value.replace(/\D/g, '').substring(0, 4); // Solo permitimos 4 dígitos
        event.target.value = valor.length > 2 ? valor.substring(0, 2) + '/' + valor.substring(2, 4) : valor; // Añadimos la barra '/'
    });
</script>


</body>
</html>

<?php 
$_SESSION['totalCarrito'] = $totalCarrito; // Guardamos el total en la sesión para usar en procesar_pago.php
?>
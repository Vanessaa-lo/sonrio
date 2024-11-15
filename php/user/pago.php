<?php
session_start();

// Verificar si el carrito tiene productos
$carrito = isset($_SESSION['carrito']) ? $_SESSION['carrito'] : [];
$totalCarrito = 0;

// Variable para los errores
$errores = [];

// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "", "sonrio");
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

// Inicializar el carrito en la sesión si no existe
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}
// Verificar si el carrito tiene productos
$carrito = isset($_SESSION['carrito']) ? $_SESSION['carrito'] : [];
$totalCarrito = 0;

// Variable para los errores
$errores = [];

// Procesar pago si es un formulario POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombreTitular = trim($_POST['nombre-titular']);
    $tarjetaNumero = preg_replace('/\D/', '', $_POST['tarjeta-numero']);
    $tarjetaExpiracion = $_POST['tarjeta-expiracion'];
    $tarjetaCvc = $_POST['tarjeta-cvc'];

    // Validaciones
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

    if (empty($errores)) {
        // Comprobar si hay productos en el carrito
        if (empty($carrito)) {
            // Si no hay productos en el carrito, mostrar mensaje y detener la ejecución
            die("No hay productos en el carrito. No se puede realizar el pedido.");
        }
    
        // Calcular el total del carrito
        $totalCarrito = 0;
        foreach ($carrito as $producto) {
            $totalCarrito += $producto['precio'] * $producto['cantidad'];
        }
    
        // Verificar si agregar $5 de envío (solo si hay productos)
        $envio = 0;
        if (!empty($carrito)) {
            $envio = 5; // Agregar $5 de envío si hay productos
        }
    
        $totalCarrito += $envio; // Agregar costo de envío al total
    
        // Insertar el pedido en la tabla 'pedidos'
        $sqlPedido = "INSERT INTO pedidos (usuario_id, carrito_id, total, estado) 
                      VALUES (?, ?, ?, ?)";
        $stmt = $conexion->prepare($sqlPedido);
    
        // Verificar si la preparación de la consulta fue exitosa
        if (!$stmt) {
            die("Error en la preparación de la consulta para el pedido: " . $conexion->error);
        }
    
        // Asumimos que el valor para 'usuario_id' y 'carrito_id' ya está disponible, si no, usa NULL
        $usuarioId = null;  // O el valor real si tienes el ID del usuario
        $carritoId = null;  // O el valor real si tienes el ID del carrito
        $estado = 'pendiente'; // Asumimos que el estado inicial es 'pendiente'
    
        $stmt->bind_param('iids', $usuarioId, $carritoId, $totalCarrito, $estado);
    
        // Ejecutar la consulta
        $stmt->execute();
        $pedidoId = $conexion->insert_id;  // Obtener el ID del pedido recién insertado
    
        // Verificar si se obtuvo un pedido ID válido
        if (!$pedidoId) {
            die("Error al obtener el ID del pedido");
        }
    
        // Insertar detalles del pedido en la tabla 'pedido_detalles'
        $sqlDetalles = "INSERT INTO pedido_detalles (pedido_id, producto_id, cantidad, precio_unitario) 
                        VALUES (?, ?, ?, ?)";
        $stmtDetalles = $conexion->prepare($sqlDetalles);
    
        // Verificar si la preparación de la consulta para los detalles fue exitosa
        if (!$stmtDetalles) {
            die("Error en la preparación de la consulta para los detalles: " . $conexion->error);
        }
    
        // Recorrer los productos del carrito para insertar los detalles
        foreach ($carrito as $producto) {
            // Verificar que el producto tenga los valores correctos
            if (isset($producto['id'], $producto['cantidad'], $producto['precio'])) {
                // Insertar cada producto en 'pedido_detalles'
                $stmtDetalles->bind_param('iiid', $pedidoId, $producto['id'], $producto['cantidad'], $producto['precio']);
                $stmtDetalles->execute();
            } else {
                // Si el producto no tiene los valores correctos, mostrar un error (opcional)
                echo "Error: Producto incompleto";
            }
        }
    
        // Verificar si la inserción de detalles fue exitosa
        if ($stmtDetalles->affected_rows <= 0) {
            die("Error al insertar los detalles del pedido");
        }
    
        // Si hay productos en el carrito, insertar el pago
        if (!empty($carrito)) {
            // Ahora insertar el pago en la tabla 'pagos'
            $sqlPago = "INSERT INTO pagos (pedido_id, monto, metodo) 
                        VALUES (?, ?, ?)";
            $stmtPago = $conexion->prepare($sqlPago);
    
            // Verificar si la preparación de la consulta para el pago fue exitosa
            if (!$stmtPago) {
                die("Error en la preparación de la consulta para el pago: " . $conexion->error);
            }
    
            // Asumimos que el monto es el total del pedido y el método de pago es 'tarjeta'
            $montoPago = $totalCarrito;  // El monto final, incluyendo los $5 de envío
            $metodoPago = 'tarjeta';  // Método de pago fijo (puedes cambiarlo si es necesario)
    
            $stmtPago->bind_param('ids', $pedidoId, $montoPago, $metodoPago);
            $stmtPago->execute();
    
            // Verificar si la inserción del pago fue exitosa
            if ($stmtPago->affected_rows <= 0) {
                die("Error al insertar el pago");
            }
        }
    
        // Confirmar que el pedido fue realizado con éxito
        echo "Pedido realizado exitosamente, ID de pedido: " . $pedidoId;

        // Limpiar el carrito y mostrar mensaje de éxito
        $_SESSION['carrito'] = [];  // Vaciar el carrito de la sesión
        $carrito = [];  // Limpiar el carrito en la variable actual
        $totalCarrito = 0;  // Reiniciar el total del carrito a 0
        $pagoExitoso = true;  // Indicar que el pago fue exitoso
    } else {
        // Si no se puede realizar el pago, establecer que el pago no fue exitoso
        $pagoExitoso = false;
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


    <!-- Sección de Pago -->
    <div class="container-pago">
    <div class="direccion">
    <button class="btn-direccion" onclick="window.location.href='mi_direccion.php'">
        Mi Dirección <i class="fas fa-chevron-right"></i>

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
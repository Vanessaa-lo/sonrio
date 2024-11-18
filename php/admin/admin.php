<?php
// Iniciar sesión
session_start();

$conexion = new mysqli("localhost", "root", "usbw", "sonrio", 3306);
if ($conexion->connect_error) {
    die("<script>Swal.fire('Error', 'Conexión fallida a la base de datos.', 'error');</script>");
}
$conexion->set_charset("utf8"); 

// Consultas para obtener datos del dashboard
$queryProductos = "SELECT COUNT(*) AS total FROM productos";
$resultProductos = $conexion->query($queryProductos);
$totalProductos = $resultProductos->fetch_assoc()['total'];

$queryUsuarios = "SELECT COUNT(*) AS total FROM usuarios";
$resultUsuarios = $conexion->query($queryUsuarios);
$totalUsuarios = $resultUsuarios->fetch_assoc()['total'];

$queryPedidos = "SELECT COUNT(*) AS total FROM pedidos WHERE estado = 'pendiente'";
$resultPedidos = $conexion->query($queryPedidos);
$totalPedidos = $resultPedidos->fetch_assoc()['total'];

// Consulta para las últimas actualizaciones
$queryActualizaciones = "SELECT tipo, descripcion FROM actualizaciones ORDER BY fecha DESC LIMIT 5";
$resultActualizaciones = $conexion->query($queryActualizaciones);

// Crecimiento mensual (simulación)
$crecimientoMensual = "23%"; // Puedes calcularlo según tus datos
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../../assets/imagenes/icon.ico" type="image/x-icon">
    <title>Admin</title>
    <link rel="stylesheet" href="../../estilo/admin.css">
    <link rel="icon" href="../../estilo/imagenes/cinta.png" type="image/x-icon">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>

<body class="admin-page">
    <!-- Contenedor principal con diseño mejorado -->
    <header class="header">
        <div class="top-bar">
            <div class="logosonrio">
                <img src="../../estilo/imagenes/logg.png" class="logosonrio" alt="Logo Tienda Kawaii">
            </div>
        </div>
    </header>

    <nav class="nav-bar">
        <ul>
            <li><a href="../user/home.php"><i class="fas fa-home"></i> Inicio</a></li>
            <li><a href="productos_admin.php"><i class="fas fa-box"></i> Productos</a></li>
            <li><a href="usuarios_admin.php"><i class="fas fa-users"></i> Usuarios</a></li>
            <li><a href="pedidos_admin.php"><i class="fas fa-shopping-cart"></i> Pedidos</a></li>
        </ul>
    </nav>

    <main class="content">
        <!-- Resumen del Dashboard -->
        <section class="dashboard-overview">
            <h2><i class="fas fa-tachometer-alt"></i> Resumen del Dashboard</h2>
            <div class="cards">
                <div class="card">
                    <i class="fas fa-box card-icon"></i>
                    <div class="card-info">
                        <h3><?php echo $totalProductos; ?></h3>
                        <p>Productos</p>
                    </div>
                </div>
                <div class="card">
                    <i class="fas fa-users card-icon"></i>
                    <div class="card-info">
                        <h3><?php echo $totalUsuarios; ?></h3>
                        <p>Usuarios Registrados</p>
                    </div>
                </div>
                <div class="card">
                    <i class="fas fa-shopping-cart card-icon"></i>
                    <div class="card-info">
                        <h3><?php echo $totalPedidos; ?></h3>
                        <p>Pedidos Pendientes</p>
                    </div>
                </div>
                <div class="card">
                    <i class="fas fa-chart-line card-icon"></i>
                    <div class="card-info">
                        <h3><?php echo $crecimientoMensual; ?></h3>
                        <p>Crecimiento Mensual</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="latest-updates">
    <h2><i class="fas fa-bell"></i> Últimas Actualizaciones</h2>
    <div class="updates">
        <?php
        $queryActualizaciones = "SELECT tipo, descripcion, fecha FROM actualizaciones ORDER BY fecha DESC LIMIT 5";
        $resultActualizaciones = $conexion->query($queryActualizaciones);

        while ($row = $resultActualizaciones->fetch_assoc()) { ?>
            <div class="update-item">
                <i class="fas fa-<?php echo ($row['tipo'] == 'usuario') ? 'user' : 'box'; ?>"></i>
                <p ><?php echo $row['descripcion']; ?></p><br><br>
                <small class="fecha-actualizacion" style="color: #c18dfc; margin-left: 10px; font-weight:strong;">
    <?php echo date("d-m-Y H:i", timestamp: strtotime($row['fecha'])); ?>
</small>

            </div>
        <?php } ?>
    </div>
</section>

    </main>

    <footer class="footer">
        <p>© 2024 Tienda Kawaii - Administrador</p>
    </footer>
</body>

</html>

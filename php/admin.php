<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../assets/imagenes/icon.ico" type="image/x-icon">
    <title>Admin</title>
    <link rel="stylesheet" href="../estilo/estilos.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>

<body class="admin-page light-mode">
    <!-- Botón para alternar entre modo claro y oscuro -->
    <button class="btn-mode" onclick="toggleMode()">
        <i class="fas fa-adjust"></i>
    </button>

    <!-- Contenedor principal con diseño mejorado -->
    <header class="header">
        <div class="top-bar">
        <div class="logosonrio">
            <img src="../estilo/imagenes/logg.png" class="logosonrio" alt="Logo Tienda Kawaii">
        </div>
            <div class="admin-tools">
                <a href="#" class="notifications"><i class="fas fa-bell"></i> Notificaciones</a>
                <a href="#" class="profile"><i class="fas fa-user-circle"></i> Perfil</a>
            </div>
        </div>
    </header>

    <nav class="nav-bar">
        <ul>
            <li><a href="home.php"><i class="fas fa-home"></i> Inicio</a></li>
            <li><a href="productos_admin.php"><i class="fas fa-box"></i> Productos</a></li>
            <li><a href="usuarios_admin.php"><i class="fas fa-users"></i> Usuarios</a></li>
            <li><a href="pedidos_admin.php"><i class="fas fa-shopping-cart"></i> Pedidos</a></li>
            <li><a href="eatadisticas_admin.php"><i class="fas fa-chart-line"></i> Estadísticas</a></li>
        </ul>
    </nav>

    <main class="content">
        <section class="dashboard-overview">
            <h2><i class="fas fa-tachometer-alt"></i> Resumen del Dashboard</h2>
            <div class="cards">
                <div class="card">
                    <i class="fas fa-box card-icon"></i>
                    <div class="card-info">
                        <h3>120</h3>
                        <p>Productos</p>
                    </div>
                </div>
                <div class="card">
                    <i class="fas fa-users card-icon"></i>
                    <div class="card-info">
                        <h3>58</h3>
                        <p>Usuarios Registrados</p>
                    </div>
                </div>
                <div class="card">
                    <i class="fas fa-shopping-cart card-icon"></i>
                    <div class="card-info">
                        <h3>35</h3>
                        <p>Pedidos Pendientes</p>
                    </div>
                </div>
                <div class="card">
                    <i class="fas fa-chart-line card-icon"></i>
                    <div class="card-info">
                        <h3>23%</h3>
                        <p>Crecimiento Mensual</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="latest-updates">
            <h2><i class="fas fa-bell"></i> Últimas Actualizaciones</h2>
            <div class="updates">
                <div class="update-item">
                    <i class="fas fa-box"></i>
                    <p>Se ha añadido un nuevo producto: "Producto XYZ"</p>
                </div>
                <div class="update-item">
                    <i class="fas fa-user"></i>
                    <p>Nuevo usuario registrado: "usuario123"</p>
                </div>
                <div class="update-item">
                    <i class="fas fa-shopping-cart"></i>
                    <p>Pedido #1234 ha sido marcado como enviado</p>
                </div>
            </div>
        </section>
    </main>

    <footer class="footer">
        <p>&copy; 2024 Admin Dashboard - Todos los derechos reservados</p>
        <div class="footer-social-icons">
            <a href="#"><i class="fab fa-facebook"></i></a>
            <a href="#"><i class="fab fa-twitter"></i></a>
            <a href="#"><i class="fab fa-instagram"></i></a>
        </div>
    </footer>

    <script src="../estilo/funciones.js"></script>
</body>

</html>
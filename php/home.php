<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos</title>
    <link href="../estilo/estilos.css" rel="stylesheet">
    <link rel="icon" href="../estilo/imagenes/cinta.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

</head>

<body>
    <!-- Navbar -->
    <div class="navbar" id="navbar-productos">
        <div class="logosonrio">
            <img src="../estilo/imagenes/logg.png" class="logosonrio" id="logo-productos"></div>
        <div class="cont-a">
            <div class="cont-a">
                <a href="home.php"><i class="fas fa-home"></i> Inicio</a>
                <a href="productos.php"><i class="fas fa-box"></i> Productos</a>
                <a href="carrito.php"><i class="fas fa-shopping-cart"></i> Carrito</a>
            </div>
        </div>
        <!-- Buscador -->
        <div class="search-bar">
            <i class="fas fa-search"></i>
            <input type="text" placeholder="que estas buscando?">
        </div>
    </div>



    <div class="container" id="home">
        <div class="container-top">
            <img src="..\estilo\imagenes\fondof.webp" alt="Imagenes" class="fondof" />
            <div class="container-left">

            </div>

        </div>
        <br></br>
        <!-- Sección de Promociones -->
        <section class="promociones">
            <h2 class="ttop">¿Quién es tu favorito?</h2>
            <div class="promocion-circulos">
                <div class="promo-item">
                    <img src="..\estilo\imagenes\rani.jpg" alt="Promo 1">
                    <h2>keroppi</h2>

                </div>
                <div class="promo-item">
                    <img src="..\estilo\imagenes\kuro.jpg" alt="Promo 2">
                    <h2>Kurommy</h2>
                </div>
                <div class="promo-item">
                    <img src="..\estilo\imagenes\myme.jpg" alt="Promo 3">
                    <h2>My Melody</h2>
                </div>
                <div class="promo-item">
                    <img src="..\estilo\imagenes\hk.jpg" alt="Promo 2">
                    <h2>Hello Kitty</h2>
                </div>
                <div class="promo-item">
                    <img src="..\estilo\imagenes\pengg.jpg" alt="Promo 3">
                    <h2>Tuxedosam</h2>
                </div>
                <div class="promo-item">
                    <img src="..\estilo\imagenes\perr.jpg" alt="Promo 3">
                    <h2>Pochaco</h2>
                </div>
            </div>
        </section>



        <div class="container-medium">
            <img src="..\estilo\imagenes\perso.webp" alt="Imagenes" class="fondop" />


        </div>
        <h2 class="ttop">
            Productos populares
        </h2>
        <div class="slider">

            <div class="cards">
                <div class="product-card2">
                    <img src="..\estilo\imagenes\kuro.png" alt="Producto 2">
                    <h3>Producto 2</h3>
                    <p>$15.00</p>

                </div>
                <div class="product-card2">
                    <img src="..\estilo\imagenes\azul.png" alt="Producto 3">
                    <h3>Producto 3</h3>
                    <p>$20.00</p>

                </div>
                <div class="product-card2">
                    <img src="..\estilo\imagenes\bunny.png" alt="Producto 3">
                    <h3>Producto 3</h3>
                    <p>$20.00</p>

                </div>

                <div class="product-card2">

                    <img src="..\estilo\imagenes\pngwing.com (1).png" alt="Producto 1">
                    <h3>Devil My melody</h3>
                    <p>$10.00</p>

                </div>
            </div>








        </div>
    </div>





    <script>
        // Manejo del carrito de compras
        function agregarAlCarrito(nombreProducto) {
            alert(nombreProducto + " ha sido agregado al carrito. ");
            // Lógica adicional para el carrito se puede implementar aquí
        }
    </script>








</body>





</html>
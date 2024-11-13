<?php
include("../db.php");

$query = $_POST['query'] ?? '';
$consulta = "SELECT id, nombre, descripcion, precio, url_imagen FROM productos WHERE nombre LIKE ?";
$stmt = $conexion->prepare($consulta);
$searchTerm = '%' . $query . '%';
$stmt->bind_param("s", $searchTerm);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {
    while ($producto = $resultado->fetch_assoc()) {
        $imagen_url = !empty($producto['url_imagen']) ? 'data:image/png;base64,' . $producto['url_imagen'] : 'ruta/a/imagen/por_defecto.png';
        $precio = !empty($producto['precio']) ? number_format($producto['precio'], 2) : 'Precio no disponible';
        $descripcion = !empty($producto['descripcion']) ? $producto['descripcion'] : 'Descripción no disponible';
        
        echo '<div class="product-card">';
        echo '    <h3>' . $producto['nombre'] . '</h3>';
        echo '    <img src="' . $imagen_url . '" alt="' . $producto['nombre'] . '">';
        echo '    <p>$' . $precio . ' MXN</p>';
        echo '    <p>' . $descripcion . '</p>';
        echo '    <form onsubmit="return agregarAlCarrito(event)">';
        echo '        <input type="hidden" name="producto_id" value="' . $producto['id'] . '">';
        echo '        <input type="hidden" name="nombre" value="' . $producto['nombre'] . '">';
        echo '        <input type="hidden" name="precio" value="' . $producto['precio'] . '">';
        echo '        <div class="item-quantity2">';
        echo '            <button type="button" class="decrease" onclick="decrementarCantidad(this)">-</button>';
        echo '            <input type="number" name="cantidad" value="1" min="1" class="quantity-input">';
        echo '            <button type="button" class="increase" onclick="incrementarCantidad(this)">+</button>';
        echo '        </div>';
        echo '        <button type="submit" class="add-to-cart-btn">Agregar al carrito</button>';
        echo '    </form>';
        echo '</div>';
    }
} else {
    echo '<p>No hay productos que coincidan con tu búsqueda.</p>';
}
?>

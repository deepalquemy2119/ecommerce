<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../public/css/productos.css">
    <title>Productos</title>
</head>
<body>

    <!-- Formulario para agregar productos -->
    <h2>Agregar Producto</h2>
    <form action="productos.php" method="POST" enctype="multipart/form-data">
        <label for="nombre">Nombre del Producto:</label>
        <input type="text" name="nombre" required><br><br>

        <label for="descripcion">Descripción:</label>
        <textarea name="descripcion" required></textarea><br><br>

        <label for="precio">Precio:</label>
        <input type="number" name="precio" step="0.01" required><br><br>

        <label for="stock">Stock:</label>
        <input type="number" name="stock" required><br><br>

        <label for="imagen">Imagen del Producto:</label>
        <input type="file" name="imagen" accept="image/*" required><br><br>

        <button type="submit">Agregar Producto</button>
    </form>

    <hr>

    <br><br><br><br>
    <!-- Mostrar productos e imágenes -->
    <h2>Lista de Productos</h2>
    <div class="gallery"> <!-- Usamos un contenedor para las cards -->
    <?php foreach ($productos as $producto): ?>
        <div class="card">
            <div class="card-img">
                <?php if ($producto['imagen']): ?>
                    <img src="data:image/jpg;base64,<?php echo base64_encode($producto['imagen']); ?>" alt="<?php echo htmlspecialchars($producto['imagen_nombre']); ?>" width="150">
                <?php else: ?>
                    <img src="../public/images/404/404.png" alt="Imagen no disponible" width="150">
                <?php endif; ?>
            </div>
            <div class="card-content">
                <h3><?php echo htmlspecialchars($producto['producto_nombre']); ?></h3>
                <p><?php echo isset($producto['descripcion']) ? htmlspecialchars($producto['descripcion']) : 'Descripción no disponible.'; ?></p>

                <!-- Botón de agregar al carrito solo si el usuario está logueado -->
                <?php if (isset($_SESSION['user_id'])): ?>
                    <form action="productos.php" method="POST">
                        <input type="hidden" name="producto_id" value="<?php echo $producto['producto_id']; ?>">
                        <button type="submit" name="agregar_al_carrito">Agregar al carrito</button>
                    </form>
                <?php else: ?>
                    <p><button disabled>Inicia sesión para comprar</button></p>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
    </div>

    <!-- Carrito -->
    <a href="../compras/carrito.php">
        <button class="cart-button">
            Carrito (<?php echo isset($_SESSION['carrito']) ? array_sum($_SESSION['carrito']) : 0; ?>)
        </button>
    </a>

</body>
</html>

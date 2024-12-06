<?php
session_start();

// usuario autenticado??
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit;
}


try {
    // base de datos utilizando PDO
    $dsn = "mysql:host=localhost;dbname=ecommerce;charset=utf8";
    $username = "root";
    $password = "";

    // conexión PDO
    $pdo = new PDO($dsn, $username, $password);
    
    // error de PDO a excepción
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // productos en el carrito del usuario
    $user_id = $_SESSION['user_id'];
    $sql = "
        SELECT c.id, p.nombre, p.precio, c.cantidad, i.imagen
        FROM carrito c
        JOIN productos p ON c.producto_id = p.id
        LEFT JOIN imagenes i ON p.id = i.producto_id
        WHERE c.usuario_id = :user_id
    ";

    // preparo y ejecuto consulta
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();

    // total
    $total = 0;

} catch (PDOException $e) {
    echo "Error al conectar o consultar la base de datos: " . $e->getMessage();
    exit;
}

//---------------------
// obtener los productos del carrito desde la base de datos
include('../../conexion/conexion.php');
$usuario_id = $_SESSION['usuario_id'];

$stmt = $conn->prepare("SELECT p.nombre, p.precio, c.cantidad
                        FROM carrito c
                        JOIN productos p ON c.producto_id = p.id
                        WHERE c.usuario_id = :usuario_id");
$stmt->bindParam(':usuario_id', $usuario_id);
$stmt->execute();

$productos_carrito = $stmt->fetchAll(PDO::FETCH_ASSOC);



?>

<!-- ------------ HTML  -------------- -->

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras</title>
    <link rel="stylesheet" href="./public/css/carrito.css">
</head>
<body>

    <h1>Carrito de Compras</h1>

    <?php if ($stmt->rowCount() > 0): ?>
        <table>
            <tr>
                <th>Imagen</th>
                <th>Producto</th>
                <th>Precio</th>
                <th>Cantidad</th>
                <th>Total</th>
                <th>Acción</th>
            </tr>

            <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): 
                $totalProducto = $row['precio'] * $row['cantidad'];
                $total += $totalProducto;
            ?>
                <tr>
                    <td>
                        <?php if ($row['imagen']): ?>
                            <img src="data:images/jpg;base64,<?php echo base64_encode($row['imagen']); ?>" alt="<?php echo htmlspecialchars($row['nombre']); ?>" width="100">
                        <?php else: ?>
                            <img src="images/404/404.png" alt="Imagen no disponible" width="100">
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                    <td><?php echo "$" . number_format($row['precio'], 2); ?></td>
                    <td><?php echo $row['cantidad']; ?></td>
                    <td><?php echo "$" . number_format($totalProducto, 2); ?></td>
                    <td>
                        <!-- Botón para eliminar producto del carrito -->
                        <a href="eliminar_carrito.php?id=<?php echo $row['id']; ?>">Eliminar</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>

        <h3>Total: $<?php echo number_format($total, 2); ?></h3>

        <div>
            <a href="checkout.php">Proceder a la compra</a>
        </div>

    <?php else: ?>
        <p>No tienes productos en tu carrito.</p>
    <?php endif; ?>

    <div>
        <a href="../../index.php">Seguir comprando</a>
    </div>

</body>
</html>

<?php
// cierro
$pdo = null;

/*     conn a la base utilizando PDO:

    uso PDO para establecer la conexion. servidor local.
    atributo PDO::ATTR_ERRMODE a PDO::ERRMODE_EXCEPTION para que PDO arroje excepciones si ocurre algún error.
    charset=utf8 asegura que la conexión maneje correctamente los caracteres especiales.

    consulta SQL:

    consulta SELECT para obtener productos del carrito del usuario.
    incluye un LEFT JOIN con la tabla imagenes para obtener la imagen asociada a cada producto.
    la cláusula WHERE filtra los productos del carrito según el usuario_id del usuario autenticado.

    uso de prepare y bindParam:

    consulta SQL se prepara usando prepare para evitar inyecciones SQL.
    parámetro :user_id es vinculado a la variable $user_id utilizando bindParam. Esto asegura que el valor del ID del usuario se inserte de forma segura en la consulta.

    manejo de consulta:

    ejecuta con execute().
    el resultado se usa con fetch(PDO::FETCH_ASSOC) dentro de un ciclo while para mostrar cada producto del carrito.

    calculo total:

    cada iteración del ciclo, se calcula el precio total de cada producto multiplicando su precio por la cantidad, y luego se suma al total general.

    mostrar productos:

    cada producto se muestra la imagen (en formato base64 si está presente en la base de datos), el nombre, el precio, la cantidad y el total por ese producto.
    Si no hay imagen, se muestra una imagen por defecto.(404 de imagen)

    delete productos del carrito:

    cada fila de producto, hay un enlace para eliminarlo del carrito. Este enlace redirige a eliminar_carrito.php, donde la lógica esta, para eliminar el producto de la base de datos.

    conexión: cierro

    conexión a la base de datos con $pdo = null;.      */



?>
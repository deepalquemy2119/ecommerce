<?php
session_start();
include 'conexion.php';

// Verificar si el usuario está logueado
if (isset($_SESSION['usuario_id'])) {
    $usuario_id = $_SESSION['usuario_id'];

    // Si el carrito tiene productos, guardarlos en la base de datos
    if (isset($_SESSION['carrito']) && !empty($_SESSION['carrito'])) {
        // Empezamos una transacción para asegurarnos de que todo se actualice correctamente
        $pdo->beginTransaction();

        try {
            foreach ($_SESSION['carrito'] as $producto_id => $cantidad) {
                // Comprobar si el producto existe
                $stmt = $pdo->prepare("SELECT * FROM productos WHERE id = :producto_id");
                $stmt->execute(['producto_id' => $producto_id]);
                $producto = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($producto) {
                    // Insertar el producto en el carrito de la base de datos
                    $stmt = $pdo->prepare("INSERT INTO carrito (usuario_id, producto_id, cantidad) VALUES (:usuario_id, :producto_id, :cantidad)");
                    $stmt->execute([
                        'usuario_id' => $usuario_id,
                        'producto_id' => $producto_id,
                        'cantidad' => $cantidad
                    ]);


// Calcular el total de la compra
$total = 0;
foreach ($_SESSION['carrito'] as $producto_id => $cantidad) {
    // Obtener el precio del producto
    $stmt = $pdo->prepare("SELECT * FROM productos WHERE id = :producto_id");
    $stmt->execute(['producto_id' => $producto_id]);
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($producto) {
        $total += $producto['precio'] * $cantidad;
    }
}

// Registrar la compra
$stmt = $pdo->prepare("INSERT INTO compras (usuario_id, total, estado) VALUES (:usuario_id, :total, 'completada')");
$stmt->execute([
    'usuario_id' => $usuario_id,
    'total' => $total
]);

// Obtener el ID de la compra recién insertada
$compra_id = $pdo->lastInsertId();

// Registrar los detalles de la compra
foreach ($_SESSION['carrito'] as $producto_id => $cantidad) {
    $stmt = $pdo->prepare("SELECT * FROM productos WHERE id = :producto_id");
    $stmt->execute(['producto_id' => $producto_id]);
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($producto) {
        $stmt = $pdo->prepare("INSERT INTO detalle_compra (compra_id, producto_id, cantidad, precio) VALUES (:compra_id, :producto_id, :cantidad, :precio)");
        $stmt->execute([
            'compra_id' => $compra_id,
            'producto_id' => $producto_id,
            'cantidad' => $cantidad,
            'precio' => $producto['precio']
        ]);
    }
}





                    // Actualizar el stock del producto
                    if ($producto['cantidad'] >= $cantidad) {
                        $nueva_cantidad = $producto['cantidad'] - $cantidad;
                        $stmt = $pdo->prepare("UPDATE productos SET cantidad = :cantidad WHERE id = :producto_id");
                        $stmt->execute([
                            'cantidad' => $nueva_cantidad,
                            'producto_id' => $producto_id
                        ]);
                    } else {
                        // Si no hay suficiente stock, lanzar un error (puedes manejar esto de otra forma)
                        throw new Exception("No hay suficiente stock para el producto: " . $producto['nombre']);
                    }
                }
            }

            // Si todo salió bien, confirmamos la transacción
            $pdo->commit();

            // Limpiar el carrito de la sesión después de guardarlo en la base de datos
            unset($_SESSION['carrito']);
        } catch (Exception $e) {
            // Si hubo un error, deshacemos la transacción
            $pdo->rollBack();
            echo "Error al procesar la compra: " . $e->getMessage();
            exit;
        }
    }

    // Cerrar la sesión
    session_unset();
    session_destroy();

    // Redirigir a la página de inicio
    header('Location: index.php');
    exit;
} else {
    // Si el usuario no está logueado, redirigir a login
    header('Location: login.php');
    exit;
}

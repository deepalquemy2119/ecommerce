<?php
session_start();

var_dump($_SESSION);
// usuario está logueado o NO es un administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] != 'admin') {
    // no es administrador, a admin.php
    header("Location: admin.php");
    exit();
} 

//las imágenes
$image_dir = './public/images/';

// los archivos de imagen del directorio
$imagenes = array_diff(scandir($image_dir), array('..', '.'));


$mensaje_bienvenida = "¡Panel de Administración!";

// imagen 404
$error_image = './public/images/404/404.png';

// description por cada imagen
$descripciones = [
    'asus_32_i9_4060' => 'Pantalla ASUS de 32" con procesador i9 y tarjeta gráfica RTX 4060.',
    'conector_super_video' => 'Conector de video de alta definición para dispositivos multimedia.',
    'ram_8_ddr4' => 'Memoria RAM DDR4 de 8GB para un rendimiento superior.',
    'monitor_32_ref_160' => 'Monitor de 32" con resolución 4K para un rendimiento increíble.',
    'mouse_genius_ergon' => 'Mouse ergonómico Genius, ideal para largas sesiones de trabajo.',
    'on_404' => 'Imagen no disponible.',
    'pendrive_32' => 'Pendrive de 32GB, rápido y confiable para tus datos.',
    'sombrero_descanso' => 'Sombrero cómodo para descansar en el sol.',
    'teclado_blanco_mec' => 'Teclado mecánico blanco con retroiluminación RGB.'
];

//--------- subida imagen y producto -------------- 
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["imagen"])) {
    $nombre = $_POST["nombre"];
    $descripcion = $_POST["descripcion"];
    $precio = $_POST["precio"];
    $stock = $_POST["stock"];
    $imagen = $_FILES["imagen"]["name"];
    $imagen_tmp = $_FILES["imagen"]["tmp_name"];
    $upload_dir = "./public/images/";

    // mover imagen al directorio
    move_uploaded_file($imagen_tmp, $upload_dir . $imagen);

    // producto en la base de datos
    $stmt = $conn->prepare("INSERT INTO productos (nombre, descripcion, precio, stock) VALUES (:nombre, :descripcion, :precio, :stock)");
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':descripcion', $descripcion);
    $stmt->bindParam(':precio', $precio);
    $stmt->bindParam(':stock', $stock);
    $stmt->execute();

    // ID del producto insertado
    $producto_id = $conn->lastInsertId();

    // imagen en la tabla de imágenes
    $stmt = $conn->prepare("INSERT INTO imagenes (nombre, producto_id) VALUES (:nombre, :producto_id)");
    $stmt->bindParam(':nombre', $imagen);
    $stmt->bindParam(':producto_id', $producto_id);
    $stmt->execute();

    echo "Producto agregado con éxito!";
}


// ---------------- ver y gestion de productos -------------

$stmt = $conn->prepare("SELECT * FROM productos");
$stmt->execute();
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
 
foreach ($productos as $producto) {
    echo "<div>";
    echo "<h3>" . htmlspecialchars($producto['nombre']) . "</h3>";
    echo "<p>" . htmlspecialchars($producto['descripcion']) . "</p>";
    echo "<p>Precio: " . htmlspecialchars($producto['precio']) . "€</p>";
    echo "<p>Stock: " . htmlspecialchars($producto['stock']) . "</p>";
    echo "<a href='editar_producto.php?id=" . $producto['id'] . "'>Editar</a> | ";
    echo "<a href='eliminar_producto.php?id=" . $producto['id'] . "'>Eliminar</a>";
    echo "</div>";
}


// proceso la opcion de crud
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    
    if ($action == 'list') {
        // función para listar los registros
        include_once 'tabla_lista.php'; 
        // el código que imprima la tabla de datos
    } elseif ($action == 'edit') {
        // formulario para editar
        include_once 'formulario_editar.php'; 
        // formulario de edición
    } elseif ($action == 'delete') {
        // formulario de eliminación
        include_once 'formulario_eliminar.php';
    }
} else {
    // si ninguna acción, mostrar una página por defecto
    echo "Por favor, seleccione una opción.";
}

//------------------------- crud -------------------------

include_once '../conexion/conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    $id = $_POST['id'] ?? null;
    $nombre = $_POST['nombre'] ?? null;
    $email = $_POST['email'] ?? null;

    if ($action == 'update' && $id) {
        // Actualizar el registro
        $query = "UPDATE tabla_registros SET nombre = :nombre, email = :email WHERE id = :id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo 'Registro actualizado correctamente.';
        } else {
            echo 'Error al actualizar el registro.';
        }
    } elseif ($action == 'delete_confirm' && $id) {
        // Eliminar el registro
        $query = "DELETE FROM tabla_registros WHERE id = :id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo 'Registro eliminado correctamente.';
        } else {
            echo 'Error al eliminar el registro.';
        }
    }
}
//var_dump($productos);
// lista de registros (tabla)
include_once '../crud_logic/tabla_lista.php';


/*  Conexión a la base de datos: Usamos PDO para la conexión en conexion.php.
tabla_lista.php: Recuperamos y mostramos los registros con prepare() y execute(), usando parámetros con PDO.
formulario_editar.php: Usamos PDO para obtener los datos del registro a editar, y pasamos los valores a un formulario.
formulario_eliminar.php: Usamos PDO para mostrar el registro que se desea eliminar y confirmar la acción.
Procesamiento de acciones en crudAdmin.php: Actualización y eliminación usando PDO con prepare() y bindParam(). 


$_POST['action']: Determina qué acción se está realizando (actualizar o eliminar).
prepare() y bindParam(): Se preparan y vinculan las consultas de actualización y eliminación de manera segura.
execute(): Ejecuta las consultas y aplica los cambios en la base de datos.


*/


?>




<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./public/css/index.css">
    <title>Panel de Administración</title>
</head>
<body>


<div id="list-content" class="content" style="display:none;">
    <!-- Aquí se muestra la tabla de datos -->
    <?php include_once '../crud_logic/tabla_lista.php'; ?>
</div>

<div id="edit-content" class="content" style="display:none;">
    <!-- Aquí se muestra el formulario de edición -->
    <?php include_once '../crud_logic/formulario_editar.php'; ?>
</div>

<div id="delete-content" class="content" style="display:none;">
    <!-- Aquí se muestra el formulario de eliminación -->
    <?php include_once '../crud_logic/formulario_eliminar.php'; ?>
</div>



<script>
    document.getElementById('action').addEventListener('change', function() {
        var action = this.value;
        var form = document.getElementById('admin-form');
        
        // tapar todo
        document.querySelectorAll('.content').forEach(function(content) {
            content.style.display = 'none';
        });
        
        // contenido según la opción
        if (action === 'list') {
            document.getElementById('list-content').style.display = 'block';
        } else if (action === 'edit') {
            document.getElementById('edit-content').style.display = 'block';
        } else if (action === 'delete') {
            document.getElementById('delete-content').style.display = 'block';
        }
    });


    document.getElementById('admin-form').addEventListener('submit', function(event) {
    event.preventDefault();  // Prevenir el envío del formulario para inspeccionar
    console.log("Action:", document.getElementById('action').value);
    this.submit();  // Reenvía el formulario después de revisar el valor
});

</script>



<header>
    <div class="container">
      
        <div class="welcome-message">
            <h1><?php echo $mensaje_bienvenida; ?></h1>
            <p>Bienvenido <?php echo $_SESSION['usuario_nombre']; ?>. Estás en el panel de administración.</p>
        </div>
</header>

<main>

<form id="admin-form" action="crudAdmin.php" method="POST">
    <label for="action">Seleccione una opción:</label>
    <select name="action" id="action">
        <option value="">Seleccione</option>
        <option value="list">Ver lista</option>
        <option value="edit">Editar</option>
        <option value="delete">Eliminar</option>
    </select>
    <button type="submit">Ejecutar</button>
</form>

        <!-- ----------------- cerrar sesión -------------- -->
        <div class="logout">
            <a href="logout.php"><button class="logout-button">Cerrar sesión</button></a>
        </div>
    </div>
        <!--------------- cards imágenes ---------------->
        <div class="gallery">
            <?php foreach ($imagenes as $imagen): ?>
                <div class="card">
                    <!-- ruta imagen con lógica de 404 si no carga -->
                    <img src="<?php echo $image_dir . $imagen; ?>" 
                         alt="Imagen producto" 
                         onerror="this.src='<?php echo $error_image; ?>';">

                    <div class="card-content">
                        <!-- todo de imagen -->
                        <h3><?php echo ucfirst(str_replace('_', ' ', pathinfo($imagen, PATHINFO_FILENAME))); ?></h3>
                        <p><?php echo isset($descripciones[pathinfo($imagen, PATHINFO_FILENAME)]) ? $descripciones[pathinfo($imagen, PATHINFO_FILENAME)] : 'Descripción no disponible.'; ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        </main>

        <footer>
        <?php if ($_SESSION['tipo_usuario'] == 'admin'): ?>
    <h2>Agregar Producto</h2>
    <form action="crudAdmin.php" method="POST" enctype="multipart/form-data">
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
<?php endif; ?>

        </footer>
       

</body>
</html>

<?php
include_once '../conexion/conexion.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $query = "SELECT * FROM productos WHERE id = :id";  // Usamos un parámetro con PDO
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    $registro = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($registro) {
        echo '<form action="crudAdmin.php" method="POST">';
        echo '<input type="hidden" name="id" value="' . htmlspecialchars($registro['id']) . '">';
        echo '<label for="nombre">Nombre:</label>';
        echo '<input type="text" name="nombre" value="' . htmlspecialchars($registro['nombre']) . '"><br>';

        echo '<label for="email">Email:</label>';
        echo '<input type="email" name="email" value="' . htmlspecialchars($registro['email']) . '"><br>';

        echo '<button type="submit" name="action" value="update">Actualizar</button>';
        echo '</form>';
    } else {
        echo 'Registro no encontrado.';
    }
} else {
    echo 'No se proporcionó un ID válido.';
}


/*      $_GET['id']:
            Se obtiene el id desde la URL (por ejemplo, crudAdmin.php?action=edit&id=1).
        $stmt->bindParam(':id', $id, PDO::PARAM_INT):
            Se asocia el valor del id a la consulta SQL de forma segura. bindParam vincula un parámetro a un valor y especifica el tipo de dato.
        $stmt->fetch(PDO::FETCH_ASSOC):
            Recupera el registro como un array asociativo.
        Formulario HTML:
            Se llena el formulario con los valores obtenidos de la base de datos. Si el registro existe, se muestra un formulario para editar los campos. Si no existe, se muestra un mensaje de error.      */



?>

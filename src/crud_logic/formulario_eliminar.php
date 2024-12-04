<?php
include_once '../conexion/conexion.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $query = "SELECT * FROM productos WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    $registro = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($registro) {
        echo '<form action="crudAdmin.php" method="POST">';
        echo '<input type="hidden" name="id" value="' . htmlspecialchars($registro['id']) . '">';
        echo '<p>¿Estás seguro de que quieres eliminar el registro con el nombre ' . htmlspecialchars($registro['nombre']) . '?</p>';
        echo '<button type="submit" name="action" value="delete_confirm">Sí, eliminar</button>';
        echo '<a href="crudAdmin.php">Cancelar</a>';
        echo '</form>';
    } else {
        echo 'Registro no encontrado.';
    }
} else {
    echo 'No se proporcionó un ID válido.';
}

/*      Similar al formulario de edición, se obtiene el id desde la URL y se utiliza para recuperar el registro que se va a eliminar.
Se muestra un formulario de confirmación donde el usuario puede elegir eliminar el registro.  

$_GET['id']:
            Se obtiene el id desde la URL (por ejemplo, crudAdmin.php?action=edit&id=1).
        $stmt->bindParam(':id', $id, PDO::PARAM_INT):
            Se asocia el valor del id a la consulta SQL de forma segura. bindParam vincula un parámetro a un valor y especifica el tipo de dato.
        $stmt->fetch(PDO::FETCH_ASSOC):
            Recupera el registro como un array asociativo.
        Formulario HTML:
            Se llena el formulario con los valores obtenidos de la base de datos. Si el registro existe, se muestra un formulario para editar los campos. Si no existe, se muestra un mensaje de error.

*/
?>

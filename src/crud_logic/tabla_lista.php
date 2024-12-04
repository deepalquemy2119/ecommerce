<?php
include_once '../conexion/conexion.php';

$query = "SELECT * FROM usuarios";// ver cual va
$stmt = $conn->prepare($query);
$stmt->execute();

$registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($registros) {
    echo '<table>';
    echo '<thead>';
    echo '<tr>';
    echo '<th>ID</th>';
    echo '<th>Nombre</th>';
    echo '<th>Email</th>';
    echo '<th>Acciones</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';

    foreach ($registros as $row) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($row['id']) . '</td>';
        echo '<td>' . htmlspecialchars($row['nombre']) . '</td>';
        echo '<td>' . htmlspecialchars($row['email']) . '</td>';
        echo '<td>';
        echo '<a href="crudAdmin.php?action=edit&id=' . $row['id'] . '">Editar</a> | ';
        echo '<a href="crudAdmin.php?action=delete&id=' . $row['id'] . '">Eliminar</a>';
        echo '</td>';
        echo '</tr>';
    }

    echo '</tbody>';
    echo '</table>';
} else {
    echo 'No hay registros para mostrar.';
}

/*       $conn->prepare($query):
        Con PDO, siempre es preferible preparar las consultas antes de ejecutarlas. Esto mejora la seguridad (evita inyecciones SQL) y permite el uso de parámetros en consultas.
    $stmt->execute(): 
        Ejecuta la consulta preparada.
    $stmt->fetchAll(PDO::FETCH_ASSOC):
        Recupera todos los resultados de la consulta en un array asociativo, donde las claves son los nombres de las columnas de la base de datos.
    Mostrar registros:
        Si los registros existen, se genera una tabla HTML con cada uno de los resultados obtenidos de la base de datos.
    htmlspecialchars():
        Esta función protege el contenido de la base de datos (como los nombres y emails) de posibles ataques de XSS (Cross-site scripting).          */


?>

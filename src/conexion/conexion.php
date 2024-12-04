<?php
$dns = 'mysql:host=localhost;dbname=ecommerce';
$username = 'root';
$password = ''; 

// $servername = "localhost";
// $username = "root";
// $password = "";
// $dbname = "ecommerce";


try {
    $conn = new PDO($dsn, $username, $password);
    // error de PDO para excepciones
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Error de conexión: ' . $e->getMessage();
    die();
}


/*     $dsn: 
    El Data Source Name (DSN) especifica el tipo de base de datos y su ubicación. En este caso, se está usando MySQL, pero se puede modificar para otros tipos de bases de datos si es necesario.
       new PDO():
    Este es el objeto que se utiliza para crear la conexión con la base de datos. PDO abstrae la conexión y maneja la base de datos sin importar el tipo de base de datos.
    $conn->setAttribute():
    Se establece que PDO lanzará excepciones si ocurre algún error (esto permite manejar errores de manera más controlada).
    catch(PDOException $e):
    Si la conexión falla, se captura la excepción y se muestra un mensaje de error.     */



?>

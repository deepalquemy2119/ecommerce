<?php

// conexión a la base de datos
$host = '127.0.0.1';
$dbname = 'ecommerce';
$username = 'root';
$password = '';

// conn base de datos usando PDO
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);  // Configuración de errores
    //echo "Conexión exitosa a la base de datos!";
} catch (PDOException $e) {
    //echo "Conexión fallida: " . $e->getMessage(); 
    //exit;
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

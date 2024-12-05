<?php
include('./src/conexion/conexion.php');  // Incluye la conexión

$conn = ();

try {
    $conn->query("SELECT 1");  // Ejecuta una simple consulta para verificar la conexión
    echo "Conexión exitosa!";
} catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
}
?>
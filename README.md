# ecommercePaso 1: 

Instalar Apache

sudo apt update
sudo apt install apache2

Inicia el servicio de Apache:

sudo systemctl start apache2

Apache se inicia automáticamente al arrancar el sistema:

sudo systemctl enable apache2

Verifica que Apache esté funcionando abriendo tu navegador web y escribiendo:

    http://localhost

    Si todo está bien, deberías ver la página predeterminada de Apache que indica que el servidor está funcionando correctamente.

Paso 2: Instalar PHP

Si planeas usar PHP para tus scripts o páginas dinámicas, debes instalar PHP y el módulo de Apache para PHP.

    Instala PHP y el módulo para Apache:

sudo apt install php libapache2-mod-php

Reinicia Apache para que se cargue el módulo PHP:

sudo systemctl restart apache2

Verifica que PHP esté funcionando creando un archivo PHP en el directorio de tu servidor web (por lo general /var/www/html/):

sudo nano /var/www/html/info.php

Y agrega lo siguiente al archivo:

<?php
phpinfo();
?>

Luego, abre en tu navegador:

    http://localhost/info.php

    Si ves la página de información de PHP, significa que todo está funcionando correctamente.

Paso 3: Crear un sistema de base de datos (si es necesario)

Si tu sistema requiere una base de datos, puedes instalar MySQL o MariaDB.

    Instala MariaDB (un fork de MySQL y completamente compatible):

sudo apt install mariadb-server

Inicia MariaDB y configura la seguridad inicial:

sudo systemctl start mariadb
sudo mysql_secure_installation

Verifica que MariaDB esté funcionando correctamente:

sudo systemctl status mariadb

Si necesitas acceder a la consola de MariaDB:

    sudo mysql -u root -p

Paso 4: Configurar el directorio de trabajo

El directorio donde Apache busca tus archivos web es /var/www/html/. Puedes cambiar la ubicación de tus archivos web o configurarlo para que apunte a un directorio específico, dependiendo de tu preferencia.

    Puedes cambiar los permisos del directorio para poder escribir en él sin ser superusuario:

    sudo chown -R $USER:$USER /var/www/html

    Luego, crea tus archivos de tu aplicación web (HTML, PHP, etc.) dentro de /var/www/html/ o cualquier otro directorio que hayas configurado.

Paso 5: Configurar Virtual Hosts (opcional)

Si deseas manejar varios proyectos o sitios web en tu servidor, puedes configurar Virtual Hosts en Apache.

    Crea un archivo de configuración para tu sitio en /etc/apache2/sites-available/:

sudo nano /etc/apache2/sites-available/mi_sitio.conf

Agrega la siguiente configuración:

<VirtualHost *:80>
    ServerAdmin webmaster@localhost
    DocumentRoot /var/www/mi_sitio
    ServerName mi_sitio.local
    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>

Habilita el sitio y reinicia Apache:

sudo a2ensite mi_sitio.conf
sudo systemctl restart apache2

Asegúrate de que tu sistema pueda resolver mi_sitio.local modificando el archivo /etc/hosts:

sudo nano /etc/hosts

Añade la siguiente línea:

127.0.0.1   mi_sitio.local

Ahora podrás acceder a tu sitio desde el navegador con:

    http://mi_sitio.local

Paso 6: Configurar HTTPS (opcional)

Si deseas servir tu aplicación web de manera segura con HTTPS, puedes usar Let's Encrypt para obtener un certificado SSL gratuito.

    Instala Certbot (herramienta para gestionar certificados SSL de Let's Encrypt):

sudo apt install certbot python3-certbot-apache

Obtén un certificado SSL:

    sudo certbot --apache

    Sigue las instrucciones para obtener el certificado y configurar automáticamente HTTPS en tu servidor.

Paso 7: Probar y desarrollar

Con todo configurado, ahora puedes empezar a desarrollar tu aplicación web directamente en el directorio de tu servidor (/var/www/html/ o el que hayas elegido), y Apache servirá tus archivos estáticos o dinámicos según sea necesario.
Resumen

    Apache: servidor web.
    PHP: para crear aplicaciones dinámicas.
    MariaDB/MySQL: base de datos (si es necesario).
    Virtual Hosts: para manejar varios proyectos o sitios.
    Certbot: para configurar HTTPS (opcional).
    

    //---------------------------------------
    Imagenes por URLs desde google drive:

    Ejemplo de una entrada en la tabla imagenes

    SQL:
    INSERT INTO imagenes (nombre_imagen, url_imagen, descripcion, producto_id)
VALUES
('foto1.jpg', 'https://drive.google.com/uc?id=xxxxxxxxxxxxxx', 'Imagen principal del producto', 1),
('foto2.jpg', 'https://drive.google.com/uc?id=yyyyyyyyyyyyyy', 'Vista lateral de la camiseta', 1);


-------
    foto1.jpg y foto2.jpg son los nombres de las imágenes.
    Las URLs (enlace de Google Drive) apuntan a las imágenes almacenadas en Google Drive.
    descripcion es opcional, pero puedes agregar una breve descripción para cada imagen (ej., "Imagen principal del producto", "Vista lateral de la camiseta").
    producto_id = 1 se refiere al ID del producto en la tabla productos al que se están asociando estas imágenes (en este caso, el producto con ID 1, que es el "Camiseta Roja").

    -------

    Implementar en elproyecto: 

    Consulta SQL:

    He actualizado la consulta SQL para hacer un LEFT JOIN entre la tabla productos y la tabla imagenes. Esto asegura que para cada producto, se obtenga la URL de la imagen (si existe).
    LEFT JOIN es importante porque asegura que se muestren todos los productos, incluso aquellos que no tienen imágenes asociadas.

SELECT p.id, p.nombre, p.descripcion, p.precio, i.url_imagen 
FROM productos p
LEFT JOIN imagenes i ON p.id = i.producto_id

Mostrar la imagen:

    Dentro del bucle foreach, he agregado una comprobación para ver si la columna url_imagen de la tabla imagenes tiene un valor. Si la URL está presente, la imagen se muestra usando el <img src="..." /> con la URL obtenida. Si no hay una URL de imagen (es decir, el producto no tiene imagen asociada), se muestra una imagen predeterminada (default-product.jpg).

    if (!empty($producto['url_imagen'])) {
        echo "<img src='" . htmlspecialchars($producto['url_imagen']) . "' alt='" . htmlspecialchars($producto["nombre"]) . "' />";
    } else {
        echo "<img src='./images/default-product.jpg' alt='Imagen no disponible' />";
    }

    Protección contra XSS:
        Estoy usando htmlspecialchars() para proteger las salidas de datos del usuario (como nombre, descripcion, etc.) y prevenir ataques de Cross-Site Scripting (XSS).

Próximos pasos para que las imágenes se vean correctamente:

    Asegúrate de tener las URLs correctas en la base de datos: Las imágenes deben estar correctamente almacenadas en Google Drive (o el servicio que uses) y las URLs deben ser accesibles desde la web. Por ejemplo:

https://drive.google.com/uc?id=xxxxxxxxxxxxxx

Verifica los permisos de acceso: Asegúrate de que las imágenes en Google Drive sean públicas o que tengas configurado un sistema de autenticación para acceder a ellas si son privadas.

Imagen por defecto: Si un producto no tiene imagen asociada en la base de datos, asegúrate de tener una imagen predeterminada (default-product.jpg) en el directorio ./images/ para evitar que el sitio se quede sin mostrar una imagen.

Desplegar el proyecto: Finalmente, sube todo el código a tu servidor web o ejecútalo localmente con un servidor PHP (como XAMPP o WAMP) para probar que las imágenes se muestran correctamente.

----------

Crear la funcion cambio Formato, para las urls de google drive: 

Para realizar la tarea de formatear automáticamente las URLs de Google Drive y hacer que funcione con el código que tienes, necesitas un método para transformar las URLs de Google Drive en enlaces de imágenes directos. Esto se puede hacer extrayendo el ID de cada URL y luego generando la URL de la imagen en el formato correcto.

En Google Drive, las URLs de las imágenes compartidas tienen el siguiente formato:

https://drive.google.com/file/d/<file_id>/view?usp=sharing

Para convertir estas URLs en enlaces directos de imagen, debemos cambiar el formato a:

https://drive.google.com/uc?export=view&id=<file_id>

Donde <file_id> es el identificador del archivo en la URL original.
Pasos para hacerlo automáticamente en el código:

    Extraer el ID del archivo de la URL de Google Drive.
    Generar la URL de la imagen en formato directo usando el ID.
    Mostrar las imágenes formateadas en index.php.

1. Código actualizado en index.php:

Vamos a modificar tu archivo index.php para incluir una función que convierta automáticamente las URLs de Google Drive en URLs de imagen directa.
index.php (modificado):

<?php
// Configuración de la base de datos
$servername = "localhost";
$username = "root"; // Por defecto en XAMPP es root
$password = ""; // En XAMPP por defecto no tiene contraseña
$dbname = "ecommerce"; // El nombre de tu base de datos

try {
    // Crear la conexión usando PDO
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // Establecer el modo de error de PDO
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Obtener los productos de la base de datos
    $sql = "SELECT p.id, p.nombre, p.descripcion, p.precio, i.url_imagen 
            FROM productos p
            LEFT JOIN imagenes i ON p.id = i.producto_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    
    // Obtener todos los productos
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
    die(); // Si hay un error, se detiene la ejecución
}

// Función para convertir la URL de Google Drive a la URL directa de la imagen
function convertirUrlDrive($url_drive) {
    // Expresión regular para extraer el ID del archivo de la URL
    if (preg_match('/https:\/\/drive\.google\.com\/file\/d\/([a-zA-Z0-9_-]+)\//', $url_drive, $matches)) {
        $file_id = $matches[1];
        // Retornar la URL directa de la imagen
        return "https://drive.google.com/uc?export=view&id=" . $file_id;
    }
    return $url_drive; // Si no es una URL válida de Google Drive, retornar la URL original
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-commerce - Bienvenido</title>
    <link rel="stylesheet" href="./public/css/index.css">
</head>
<body>
    <header>
        <!-- Barra superior con botones de Login y Register -->
        <div class="top-bar">
            <a href="login.php" class="btn">Login</a>
            <a href="register.php" class="btn">Register</a>
        </div>
        <!-- Mensaje de bienvenida -->
        <h1>¡Bienvenido a nuestro E-commerce!</h1>
    </header>

    <main>
        <!-- Mostrar los productos -->
        <div class="product-list">
            <?php
            if (count($productos) > 0) {
                // Mostrar cada producto
                foreach ($productos as $producto) {
                    echo "<div class='product'>";
                    
                    // Verificar si la URL de la imagen existe
                    if (!empty($producto['url_imagen'])) {
                        // Convertir la URL de Google Drive a la URL directa de la imagen
                        $url_imagen = convertirUrlDrive($producto['url_imagen']);
                        echo "<img src='" . htmlspecialchars($url_imagen) . "' alt='" . htmlspecialchars($producto["nombre"]) . "' />";
                    } else {
                        // Si no hay imagen, mostrar una imagen por defecto
                        echo "<img src='./images/default-product.jpg' alt='Imagen no disponible' />";
                    }
                    
                    echo "<h2>" . htmlspecialchars($producto["nombre"]) . "</h2>";
                    echo "<p>" . htmlspecialchars($producto["descripcion"]) . "</p>";
                    echo "<p>Precio: $ " . number_format($producto["precio"], 2) . "</p>";
                    echo "</div>";
                }
            } else {
                echo "<p>No hay productos disponibles.</p>";
            }
            ?>
        </div>
    </main>

    <footer>
        <p>&copy; 2024 E-commerce</p>
    </footer>
</body>
</html>

<?php
// Cerrar la conexión
$conn = null;
?>

Explicación del código:

    Función convertirUrlDrive($url_drive):
        Esta función toma la URL de Google Drive y utiliza una expresión regular para extraer el file_id de la URL.
        Luego, construye y devuelve la URL de la imagen en formato directo de Google Drive: https://drive.google.com/uc?export=view&id=<file_id>.

    Uso de la función en el HTML:
        En la parte donde se muestran las imágenes, se llama a convertirUrlDrive($producto['url_imagen']) para convertir la URL de Google Drive en una URL válida de imagen.
        Si la URL es válida, se muestra la imagen; de lo contrario, se muestra una imagen predeterminada.

2. Ingresar las URLs de Google Drive en la base de datos:

Si las URLs que estás almacenando en la base de datos son del tipo:

https://drive.google.com/file/d/<file_id>/view?usp=sharing

No tienes que hacer nada manualmente para formatearlas en el código, ya que el código que hemos agregado (convertirUrlDrive) lo hará automáticamente cada vez que se cargue la página.


-- Crear la base de datos 'ecommerce'
CREATE DATABASE IF NOT EXISTS ecommerce;

-- Usar la base de datos 'ecommerce'
USE ecommerce;

-- Crear tabla de usuarios
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    password VARCHAR(255),
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Crear tabla de productos
CREATE TABLE IF NOT EXISTS productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100),
    descripcion TEXT,
    precio DECIMAL(10, 2)
);

-- Crear tabla de imágenes
CREATE TABLE IF NOT EXISTS imagenes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_imagen VARCHAR(255),
    url_imagen VARCHAR(255),
    fecha_subida TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    descripcion TEXT,
    producto_id INT,
    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE
);

-- Crear tabla de carrito de compras
CREATE TABLE IF NOT EXISTS carrito (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    producto_id INT,
    cantidad INT DEFAULT 1,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    FOREIGN KEY (producto_id) REFERENCES productos(id)
);

-- Crear tabla de inicios de sesión
CREATE TABLE IF NOT EXISTS logins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- Procedimiento almacenado para insertar productos e imágenes
DELIMITER $$

CREATE PROCEDURE insertar_productos_y_imagenes()
BEGIN
    -- Declarar variables
    DECLARE url_imagen VARCHAR(255);
    DECLARE nombre_producto VARCHAR(100);
    DECLARE descripcion_imagen VARCHAR(255);
    DECLARE precio_producto DECIMAL(10,2);
    DECLARE producto_id INT;
    DECLARE done INT DEFAULT 0;

    -- Cursor para recorrer las URLs de las imágenes
    DECLARE imagen_cursor CURSOR FOR
    SELECT url_imagen FROM imagenes;

    -- Manejador para manejar la condición de fin de cursor
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

    -- Abrir el cursor
    OPEN imagen_cursor;

    -- Bucle para recorrer las URLs de las imágenes
    read_loop: LOOP
        -- Obtener la URL de la imagen
        FETCH imagen_cursor INTO url_imagen;

        -- Si no hay más imágenes, terminamos el bucle
        IF done THEN
            LEAVE read_loop;
        END IF;

        -- Extraer el nombre del producto de la URL (esto es solo una aproximación)
        -- Se obtiene el nombre de la imagen (sin extensión) como nombre del producto
        SET nombre_producto = REPLACE(SUBSTRING_INDEX(SUBSTRING_INDEX(url_imagen, '/', -1), '.', 1), '_', ' ');

        -- Insertar el producto en la tabla productos
        SET precio_producto = ROUND(RAND()*100 + 50, 2);  -- Precio aleatorio entre 50 y 150
        INSERT INTO productos (nombre, descripcion, precio)
        VALUES (nombre_producto, CONCAT('Descripción de ', nombre_producto), precio_producto);

        -- Obtener el ID del producto insertado
        SET producto_id = LAST_INSERT_ID();

        -- Insertar la imagen asociada al producto
        SET descripcion_imagen = CONCAT('Imagen de ', nombre_producto);
        INSERT INTO imagenes (nombre_imagen, url_imagen, descripcion, producto_id)
        VALUES (nombre_producto, url_imagen, descripcion_imagen, producto_id);

    END LOOP;

    -- Cerrar el cursor
    CLOSE imagen_cursor;
END $$

DELIMITER ;

-- Ejecutar el procedimiento para insertar productos e imágenes
-- Asegúrate de ejecutar este comando para insertar los productos e imágenes
-- CALL insertar_productos_y_imagenes();


-- Instrucciones

--     Ejecutar el código en tu servidor MySQL: Pega todo el código en el área de consultas de tu herramienta de administración de base de datos (phpMyAdmin, MySQL Workbench, etc.).
--         Este código crea las tablas necesarias (usuarios, productos, imagenes, carrito, logins).
--         Crea el procedimiento almacenado llamado insertar_productos_y_imagenes que inserta productos en la tabla productos y las imágenes asociadas en la tabla imagenes.

--     Ejecutar el procedimiento para insertar los datos: Después de haber creado las tablas y el procedimiento, ejecuta el siguiente comando en tu consola SQL para insertar los productos e imágenes:

-- CALL insertar_productos_y_imagenes();

-- Verifica que los datos se han insertado correctamente: Para verificar los datos insertados, puedes ejecutar estas consultas en tu consola de SQL:

--     Ver los productos insertados:

-- SELECT * FROM productos;

-- Ver las imágenes asociadas a los productos:

--         SELECT * FROM imagenes;

-- Explicación de los cambios

--     Cursor: El procedimiento usa un cursor (imagen_cursor) para recorrer todas las URLs de las imágenes que están en la tabla imagenes y luego inserta productos e imágenes asociados.
--     Extracción del nombre del producto: Se utiliza la URL de la imagen para extraer el nombre del producto (esto es solo una aproximación, basándose en el nombre del archivo).
--     Precio aleatorio: Los productos tienen un precio aleatorio entre 50 y 150.

-- Si tienes problemas, verifica lo siguiente

--     Datos existentes: Si las tablas ya tienen datos o están mal configuradas, puedes limpiar las tablas usando los comandos DELETE o DROP como te expliqué anteriormente.
--     Procedimiento no ejecutado: Asegúrate de que el procedimiento insertar_productos_y_imagenes se haya creado correctamente. Si ves algún error, asegúrate de que las tablas estén bien creadas antes de ejecutar el procedimiento.

-- Este SQL completo debería funcionar sin problemas para crear la base de datos, las tablas, y permitirte insertar productos e imágenes en ellas usando el procedimiento almacenado.
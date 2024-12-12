-- Crear la base de datos 'ecommerce'
CREATE DATABASE IF NOT EXISTS ecommerce;

-- Usar la base de datos 'ecommerce'
USE ecommerce;

-- Crear la tabla 'usuarios'
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,  -- ID único y autoincremental
    email VARCHAR(255) NOT NULL UNIQUE,  -- Email único para cada usuario
    nameuser VARCHAR(255) NOT NULL UNIQUE,  -- Nombre de usuario único
    password VARCHAR(255) NOT NULL,  -- Contraseña en texto cifrado
    tipo_usuario ENUM('cliente', 'admin') NOT NULL,  -- Tipo de usuario
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP  -- Fecha de creación automática
);

-- Crear la tabla 'productos'
CREATE TABLE IF NOT EXISTS productos (
    id INT AUTO_INCREMENT PRIMARY KEY,  -- ID único y autoincremental
    nombre VARCHAR(255) NOT NULL,  -- Nombre del producto
    descripcion TEXT,  -- Descripción del producto
    precio DECIMAL(10, 2) NOT NULL,  -- Precio del producto
    stock INT NOT NULL,  -- Cantidad en stock
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP  -- Fecha de creación automática
);

-- Crear la tabla 'imagenes' para almacenar imágenes en formato binario
CREATE TABLE IF NOT EXISTS imagenes (
    id INT AUTO_INCREMENT PRIMARY KEY,  -- ID único y autoincremental
    nombre VARCHAR(255) NOT NULL,  -- Nombre de la imagen
    imagen LONGBLOB NOT NULL,  -- Imagen en formato binario (BLOB)
    producto_id INT,  -- ID del producto al que pertenece la imagen
    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE  -- Relación con la tabla productos
);

-- Crear la tabla 'carrito' para gestionar los productos del carrito de compras
CREATE TABLE IF NOT EXISTS carrito (
    id INT AUTO_INCREMENT PRIMARY KEY,  -- ID único y autoincremental
    usuario_id INT NOT NULL,  -- ID del usuario que tiene el carrito
    producto_id INT NOT NULL,  -- ID del producto en el carrito
    cantidad INT NOT NULL,  -- Cantidad de ese producto en el carrito
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,  -- Relación con la tabla usuarios
    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE  -- Relación con la tabla productos
);

-- Crear la tabla 'sesiones' para gestionar las sesiones de los usuarios
CREATE TABLE IF NOT EXISTS sesiones (
    id INT AUTO_INCREMENT PRIMARY KEY,  -- ID único y autoincremental
    usuario_id INT NOT NULL,  -- ID del usuario que está en la sesión
    session_id VARCHAR(255) NOT NULL,  -- ID de la sesión
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,  -- Fecha de creación de la sesión
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE  -- Relación con la tabla usuarios
);


-- procedure para insertar un producto
DELIMITER $$
CREATE PROCEDURE `insertar_producto` (
    IN nombre_producto VARCHAR(255),
    IN descripcion_producto TEXT,
    IN precio_producto DECIMAL(10,2),
    IN stock_producto INT
)
BEGIN
    INSERT INTO productos (nombre, descripcion, precio, stock)
    VALUES (nombre_producto, descripcion_producto, precio_producto, stock_producto);
END$$
DELIMITER ;

-- procedure para obtener un producto por su ID
DELIMITER $$
CREATE PROCEDURE `obtener_producto` (
    IN id_producto INT
)
BEGIN
    SELECT * FROM productos WHERE id = id_producto;
END$$
DELIMITER ;

-- procedure para actualizar un producto
DELIMITER $$
CREATE PROCEDURE `actualizar_producto` (
    IN id_producto INT,
    IN nombre_producto VARCHAR(255),
    IN descripcion_producto TEXT,
    IN precio_producto DECIMAL(10,2),
    IN stock_producto INT
)
BEGIN
    UPDATE productos
    SET nombre = nombre_producto,
        descripcion = descripcion_producto,
        precio = precio_producto,
        stock = stock_producto
    WHERE id = id_producto;
END$$
DELIMITER ;

-- procedure para eliminar un producto
DELIMITER $$
CREATE PROCEDURE `eliminar_producto` (
    IN id_producto INT
)
BEGIN
    DELETE FROM productos WHERE id = id_producto;
END$$
DELIMITER ;


-- procedure almacenado para insertar una imagen
DELIMITER $$

CREATE PROCEDURE `insertar_imagen`(
    IN nombre_imagen VARCHAR(255),
    IN archivo_imagen LONGBLOB,
    IN producto_id INT
)
BEGIN
    -- Insertar la imagen en la tabla 'imagenes'
    INSERT INTO imagenes (nombre, imagen, producto_id)
    VALUES (nombre_imagen, archivo_imagen, producto_id);
END $$

DELIMITER ;

-- procedure para obtener las imágenes de un producto
DELIMITER $$
CREATE PROCEDURE `obtener_imagenes_producto` (
    IN id_producto INT
)
BEGIN
    SELECT * FROM imagenes WHERE producto_id = id_producto;
END$$
DELIMITER ;

-- procedure para eliminar una imagen
DELIMITER $$
CREATE PROCEDURE `eliminar_imagen` (
    IN id_imagen INT
)
BEGIN
    DELETE FROM imagenes WHERE id = id_imagen;
END$$
DELIMITER ;


-- Procedure para insertar un usuario
DELIMITER $$

CREATE PROCEDURE `insertar_usuario` (
    IN email_usuario VARCHAR(255),
    IN nameuser_usuario VARCHAR(255),
    IN password_usuario VARCHAR(255),
    IN tipo_usuario ENUM('cliente','admin')
)
BEGIN
    INSERT INTO usuarios (email, nameuser, password, tipo_usuario)
    VALUES (email_usuario, nameuser_usuario, password_usuario, tipo_usuario);
END$$

DELIMITER ;



-- procedure para obtener un usuario por su ID
DELIMITER $$
CREATE PROCEDURE `obtener_usuario` (
    IN id_usuario INT
)
BEGIN
    SELECT * FROM usuarios WHERE id = id_usuario;
END$$
DELIMITER ;

-- procedure actualizar un usuario
DELIMITER $$
CREATE PROCEDURE `actualizar_usuario` (
    IN id_usuario INT,
    IN email_usuario VARCHAR(255),
    IN nameuser_usuario VARCHAR(255),
    IN password_usuario VARCHAR(255),
    IN tipo_usuario ENUM('cliente','admin')
)
BEGIN
    UPDATE usuarios
    SET email = email_usuario,
        nameuser = nameuser_usuario,
        password = password_usuario,
        tipo_usuario = tipo_usuario
    WHERE id = id_usuario;
END$$
DELIMITER ;

-- procedure eliminar un usuario
DELIMITER $$
CREATE PROCEDURE `eliminar_usuario` (
    IN id_usuario INT
)
BEGIN
    DELETE FROM usuarios WHERE id = id_usuario;
END$$
DELIMITER ;

-- Procedure para insertar una sesión (verificando que el usuario exista)
DELIMITER $$

CREATE PROCEDURE `insertar_sesion` (
    IN usuario_id INT,
    IN session_id VARCHAR(255)
)
BEGIN
    -- Verificar que el usuario exista antes de insertar en sesiones
    IF EXISTS (SELECT 1 FROM usuarios WHERE id = usuario_id) THEN
        INSERT INTO sesiones (usuario_id, session_id)
        VALUES (usuario_id, session_id);
    ELSE
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Usuario no encontrado.';
    END IF;
END$$

DELIMITER ;
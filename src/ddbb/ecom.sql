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

-- Procedimiento almacenado para insertar una imagen
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

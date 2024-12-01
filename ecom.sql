-- Crear la base de datos 'ecommerce'
CREATE DATABASE IF NOT EXISTS ecommerce;

-- Usar la base de datos 'ecommerce'
USE ecommerce;

-- Crear la tabla 'usuarios'
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    nameuser VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    tipo_usuario ENUM('cliente', 'admin') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Crear la tabla 'productos'
CREATE TABLE productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10, 2) NOT NULL,
    stock INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Crear la tabla 'imagenes'
CREATE TABLE imagenes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    imagen LONGBLOB NOT NULL,
    producto_id INT,
    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE  
    );

-- Procedimiento para insertar imagen
DELIMITER $$

CREATE PROCEDURE `insertar_imagen`(
    IN nombre_imagen VARCHAR(255),
    IN archivo_imagen LONGBLOB,
    IN producto_id INT
)
BEGIN
    INSERT INTO imagenes (nombre, imagen, producto_id)
    VALUES (nombre_imagen, archivo_imagen, producto_id);
END $$

DELIMITER ;

-- Crear la tabla 'carrito'
CREATE TABLE carrito (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    producto_id INT NOT NULL,
    cantidad INT NOT NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE
);

-- Crear la tabla 'sesiones'
CREATE TABLE sesiones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    session_id VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

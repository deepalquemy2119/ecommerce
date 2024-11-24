-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 23-11-2024 a las 21:57:12
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `ecommerce`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carrito`
--

CREATE TABLE `carrito` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `producto_id` int(11) DEFAULT NULL,
  `cantidad` int(11) DEFAULT NULL,
  `fecha_agregado` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `carrito`
--

INSERT INTO `carrito` (`id`, `usuario_id`, `producto_id`, `cantidad`, `fecha_agregado`) VALUES
(3, 1, 6, 1, '2024-11-22 01:11:47'),
(4, 3, 3, 2, '2024-11-22 09:26:50'),
(5, 3, 2, 2, '2024-11-22 09:26:53'),
(6, 3, 1, 6, '2024-11-22 09:26:55');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cuentas_sesiones`
--

CREATE TABLE `cuentas_sesiones` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `session_id` varchar(255) DEFAULT NULL,
  `fecha_inicio` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_expiracion` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cuentas_sesiones`
--

INSERT INTO `cuentas_sesiones` (`id`, `usuario_id`, `session_id`, `fecha_inicio`, `fecha_expiracion`) VALUES
(2, 3, 'ggrqkbgemd0nap0hl5dc8jsmif', '2024-11-22 09:22:53', '2024-11-22 13:52:53'),
(5, 3, '5hlcpetvcag9fmeg4sd81vlpmt', '2024-11-23 20:45:18', '2024-11-24 01:15:18');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `direcciones`
--

CREATE TABLE `direcciones` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `ciudad` varchar(100) DEFAULT NULL,
  `codigo_postal` varchar(10) DEFAULT NULL,
  `pais` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL,
  `categoria` varchar(50) DEFAULT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `nombre`, `descripcion`, `precio`, `categoria`, `imagen`, `fecha_creacion`) VALUES
(1, 'Teclado Blanco Mecánico', 'Teclado mecánico blanco con retroiluminación', 79.99, 'Periféricos', 'teclado-blanco-mecanico-R.jpg', '2024-11-22 00:36:55'),
(2, 'Sombrero Copa Negro para Concentrarse', 'Sombrero negro de copa alta para concentrarse', 39.99, 'Accesorios', 'sombrero-copa-negro-para-concentrarse.jpg', '2024-11-22 00:36:55'),
(3, 'Pendrive 32GB', 'Pendrive de 32 GB para almacenamiento rápido', 14.99, 'Almacenamiento', 'pendrive-32-gigas-32r_w.jpg', '2024-11-22 00:36:55'),
(4, 'Mouse Genius Ergonómico', 'Mouse ergonómico de la marca Genius, ideal para largas sesiones de uso', 24.99, 'Periféricos', 'mouse-genius-ergonomico.jpg', '2024-11-22 00:36:55'),
(5, 'Monitor 32 Pulgadas 160Hz', 'Monitor de 32 pulgadas con frecuencia de actualización de 160Hz', 399.99, 'Monitores', 'monitor-160-refresh-32-pulgadas.jpg', '2024-11-22 00:36:55'),
(6, 'Memo RAM 8GB DDR4', 'Memoria RAM de 8GB DDR4 para PC de alto rendimiento', 49.99, 'Componentes', 'memo-ram-8gigas-ddr4.jpg', '2024-11-22 00:36:55'),
(7, 'Conector Supervideo 600GB', 'Conector supervideo de alta velocidad de 600 GB', 19.99, 'Accesorios', 'conector-supervideo-600gb.jpg', '2024-11-22 00:36:55'),
(8, 'Cable USB 2m', 'Cable USB de 2 metros para dispositivos electrónicos', 9.99, 'Accesorios', 'cable-usb-2mts.jpg', '2024-11-22 00:36:55'),
(9, 'Asus Laptop 32GB i9 4060 12GB', 'Laptop Asus con procesador i9, 32GB de RAM y tarjeta gráfica RTX 4060 de 12GB', 1499.99, 'Computadoras', 'asus-32-gigas-i9-4060-12gigas.jpg', '2024-11-22 00:36:55');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `stock`
--

CREATE TABLE `stock` (
  `id` int(11) NOT NULL,
  `producto_id` int(11) DEFAULT NULL,
  `cantidad` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `estado` enum('activo','inactivo') DEFAULT 'activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `email`, `password`, `fecha_registro`, `estado`) VALUES
(1, 'gonza', 'gonza@gmail.com', '$2y$10$356Kj/cg3cfxnZ2TGX4pT.pgvZr8XEd4yoXqm/KB3h51jFhFJ1.1i', '2024-11-22 00:40:13', 'activo'),
(2, 'mar', 'mari@gmail.com', '$2y$10$m0MqEyx7nN3s8.WNSho7GuHysF93pgPIylYrVTHyWuHkAqMLSJ5DW', '2024-11-22 00:57:13', 'activo'),
(3, 'buho-13', 'buho-13@gmail.com', '$2y$10$ZpiyTMtcxwNk21UnwScaoeW6uDQ.40zuUJPE6e0JM3jl8xZ3Dn5ki', '2024-11-22 09:22:36', 'activo'),
(4, 'mate-32', 'mate-32@gmail.com', '$2y$10$6L6sSzjoeRiRwG3eZLdmGeGevLOG9C2CPmbVIF3ET7xdHvxLMpwfa', '2024-11-22 21:46:22', 'activo');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `carrito`
--
ALTER TABLE `carrito`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `producto_id` (`producto_id`);

--
-- Indices de la tabla `cuentas_sesiones`
--
ALTER TABLE `cuentas_sesiones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `direcciones`
--
ALTER TABLE `direcciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `stock`
--
ALTER TABLE `stock`
  ADD PRIMARY KEY (`id`),
  ADD KEY `producto_id` (`producto_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `carrito`
--
ALTER TABLE `carrito`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `cuentas_sesiones`
--
ALTER TABLE `cuentas_sesiones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `direcciones`
--
ALTER TABLE `direcciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `stock`
--
ALTER TABLE `stock`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `carrito`
--
ALTER TABLE `carrito`
  ADD CONSTRAINT `carrito_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `carrito_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`);

--
-- Filtros para la tabla `cuentas_sesiones`
--
ALTER TABLE `cuentas_sesiones`
  ADD CONSTRAINT `cuentas_sesiones_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `direcciones`
--
ALTER TABLE `direcciones`
  ADD CONSTRAINT `direcciones_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `stock`
--
ALTER TABLE `stock`
  ADD CONSTRAINT `stock_ibfk_1` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;


CREATE TABLE compras (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    total DECIMAL(10, 2),
    estado ENUM('pendiente', 'completada', 'cancelada') DEFAULT 'pendiente',
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

CREATE TABLE detalle_compra (
    id INT AUTO_INCREMENT PRIMARY KEY,
    compra_id INT,
    producto_id INT,
    cantidad INT,
    precio DECIMAL(10, 2),
    FOREIGN KEY (compra_id) REFERENCES compras(id),
    FOREIGN KEY (producto_id) REFERENCES productos(id)
);

CREATE TABLE `admins` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `usuario_id` INT(11) NOT NULL,
    `rol` ENUM('superadmin', 'admin') DEFAULT 'admin',  -- Puede tener 'superadmin' o 'admin' como roles
    `fecha_creacion` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
);


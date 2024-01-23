-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3307
-- Tiempo de generación: 23-01-2024 a las 12:41:02
-- Versión del servidor: 10.4.28-MariaDB
-- Versión de PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `alameda_motor`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `total_price` float(10,2) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `status` enum('1','0') NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `orders`
--

INSERT INTO `orders` (`id`, `usuario_id`, `total_price`, `created`, `modified`, `status`) VALUES
(37, 12, 51.00, '2024-01-23 12:20:41', '2024-01-23 12:20:41', '1');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `quantity` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `producto_id`, `quantity`) VALUES
(10, 37, 3, 1),
(11, 37, 4, 1),
(12, 37, 12, 1),
(13, 37, 7, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `nombre_producto` varchar(255) NOT NULL,
  `descripcion` varchar(255) NOT NULL,
  `precio` decimal(5,2) NOT NULL,
  `existencias` int(11) NOT NULL,
  `categoria` enum('Mantenimiento','Mecánica','Herramientas') NOT NULL,
  `imagen_producto` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `nombre_producto`, `descripcion`, `precio`, `existencias`, `categoria`, `imagen_producto`) VALUES
(1, 'Aceite de motor 5W-30', 'Aceite de motor viscosidad 5W-30', 21.90, 50, 'Mantenimiento', 'imgs/Products/Aceite-motor.jpg'),
(2, 'Filtro de aceite', 'Filtro de aceite para motor', 12.99, 49, 'Mecánica', 'imgs/Products/Filtro-aceite.jpg'),
(3, 'Filtro de aire', 'Filtro de aire de alto flujo', 13.95, 49, 'Mecánica', 'imgs/Products/Filtro-aire.jpg'),
(4, 'Limpiaparabrisas', 'Limpiaparabrisas universal', 8.00, 50, 'Mantenimiento', 'imgs/Products/Limpia-parabrisas.jpg'),
(5, 'Anticongelante', 'Anticongelante para radiadores', 9.99, 50, 'Mantenimiento', 'imgs/Products/Liquido-anticong.jpg'),
(6, 'Líquido de frenos', 'Líquido de frenos DOT 4', 12.90, 48, 'Mantenimiento', 'imgs/Products/Liquido-freno.jpg'),
(7, 'Pack de limpieza.', 'Kit de limpieza para interiores de vehículos', 15.50, 50, 'Mantenimiento', 'imgs/Products/pack-limpieza.jpg'),
(8, 'Pack de herramientas.', 'Kit de herramientas básicas para vehículos', 19.99, 50, 'Herramientas', 'imgs/Products/juego-herramientas.jpg'),
(9, 'Cables de arranque', 'Cables de arranque para batería de vehículo', 21.00, 50, 'Herramientas', 'imgs/Products/Cable-arranque.jpg'),
(10, 'Batería coche', 'Batería para vehículo 12V', 98.99, 50, 'Mecánica', 'imgs/Products/batería-coche.jpg'),
(11, 'Pack de fusibles', 'Set de fusibles para vehículo', 5.00, 50, 'Mantenimiento', 'imgs/Products/juego-fusibles.jpg'),
(12, 'Limpiador de inyectores', 'Limpiador de inyectores para motor', 13.99, 50, 'Mantenimiento', 'imgs/Products/Limpiador-inyectores.jpg');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `dni` varchar(255) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `apellido1` varchar(255) NOT NULL,
  `apellido2` varchar(255) NOT NULL,
  `correo` varchar(255) NOT NULL,
  `telefono` varchar(9) DEFAULT NULL,
  `contraseña` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `dni`, `nombre`, `apellido1`, `apellido2`, `correo`, `telefono`, `contraseña`) VALUES
(12, '77666240R', 'Prueba', 'Apellido1', 'Apellido2', 'correo1010@correo.com', '685858974', '$2y$10$T1Wil8tWqkQrRdasv6YGruxyfxrSmHwQ/7Ft/FEbUzevkgNmLoFea'),
(14, '77888585L', 'Javier', 'Chaves', 'Vico', 'mail919@mail.com', '652232124', '$2y$10$2dB8fJa/u3Grs4KI.DzyAegDg3Obz2nH.QvhY1fDonPhI9KM5IIMO');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `fk_order_items_productos` (`producto_id`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `dni` (`dni`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT de la tabla `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Filtros para la tabla `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_order_items_productos` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Июн 10 2025 г., 03:21
-- Версия сервера: 8.0.30
-- Версия PHP: 8.1.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `kursovaya`
--

-- --------------------------------------------------------

--
-- Структура таблицы `cart`
--

CREATE TABLE `cart` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_price` decimal(10,2) NOT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `orders`
--

CREATE TABLE `orders` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `customer_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `address` text COLLATE utf8mb4_general_ci NOT NULL,
  `comments` text COLLATE utf8mb4_general_ci,
  `order_data` text COLLATE utf8mb4_general_ci NOT NULL COMMENT 'JSON-данные заказа',
  `total` decimal(10,2) NOT NULL,
  `status` enum('pending','accepted','rejected') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `customer_name`, `phone`, `address`, `comments`, `order_data`, `total`, `status`, `created_at`) VALUES
(1, 20, 'asdfasdfasdf', '214124214', 'sadfasdfsadf', 'sdafasdfsadfasdf', '[{\"id\":\"22\",\"name\":\"\\u0441\\u0443\\u043f\\u0435\\u0440\\u043b\\u0430\\u0439\\u04422\",\"price\":\"18000.00\",\"quantity\":1},{\"id\":\"23\",\"name\":\"Razer\",\"price\":\"25000.00\",\"quantity\":1}]', '43000.00', 'rejected', '2025-06-09 23:54:04'),
(2, 20, 'Куценко Владислав Дмитриевич', '+79322394981', 'Гагарина 32', '', '[{\"id\":\"22\",\"name\":\"\\u0441\\u0443\\u043f\\u0435\\u0440\\u043b\\u0430\\u0439\\u04422\",\"price\":\"18000.00\",\"quantity\":2},{\"id\":\"24\",\"name\":\"G pro\",\"price\":\"19000.00\",\"quantity\":2}]', '74000.00', 'accepted', '2025-06-09 23:59:33'),
(3, 20, 'Ермаков Артём ', '+7999999999', 'Гагарина 39', 'БУБУБУБУББУБУ', '[{\"id\":\"23\",\"name\":\"Razer\",\"price\":\"25000.00\",\"quantity\":1}]', '25000.00', 'pending', '2025-06-10 00:09:51');

-- --------------------------------------------------------

--
-- Структура таблицы `products`
--

CREATE TABLE `products` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `category` enum('keyboard','mouse','headphones') NOT NULL,
  `image` varchar(500) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `category`, `image`, `created_at`) VALUES
(22, 'суперлайт2', 'сенсор 3395 6 кнопок 26.000dpi', '18000.00', 'mouse', 'uploads/products/product_680590c454912.webp', '2025-04-21 00:26:44'),
(23, 'Razer', '100% клавиатура механическая', '25000.00', 'keyboard', 'uploads/products/product_680590f3427b8.jpg', '2025-04-21 00:27:31'),
(24, 'G pro', 'беспроводные/проводные, держат заряд до 80 часов', '19000.00', 'headphones', 'uploads/products/product_680591213fd72.jpg', '2025-04-21 00:28:17');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `login` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `pass` varchar(255) DEFAULT NULL,
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `is_admin` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `email`, `login`, `pass`, `username`, `phone`, `role`, `is_admin`) VALUES
(19, 'kuc1k@list.ru', 'kucik', '$2y$10$jJ0hrFs/hQdBC46bdkbh9.EYDRUSybo14oNpf06g/cnFa9bnTwVpi', 'Vladislav', '89322304921', 'user', 0),
(20, 'kuc1k@mail.ru', 'kuc1k', '$2y$10$j50XaiZQqKn/2cHrxFO2kuObI9E/QTFGRP6tr52QxJTIv02fLpEE.', 'vladislav', '+79995817447', 'user', 0),
(21, 'vlad@mail.ru', 'kuc1k2', '$2y$10$qwJiiJ/c9d3cHJRkt94SnOzu4u5sRM0m2hrG8WWneet0OZc8aVxMq', 'vasdasd', '+79322304981', 'user', 0),
(22, 'kakak@mail.ru', 'kuc1k3', '$2y$10$92Ziz4KV1Pgt9GsymJvTuO5c7SddcXefGM8xS7qgXa7TSatDknen2', 'Владислав Куценко', '8888888888', 'user', 0),
(23, 'admin@example.com', 'admin', '$2y$10$FLJzRtmUUsCkicvq7wqHWOn7uuqWzmwmF7AyMzKDU/xI2wA/CMYeq', 'Admin', NULL, 'admin', 0),
(24, 'kucenko.z@inbox.ru', 'aqwww', '$2y$10$vlUphS05LWEi3pXlEpVW6eYVBSU4NnTU9D0evstCQmKgTCYSCZVQC', 'куценко евгений дмитриевич', NULL, 'user', 0),
(25, 'fadsfsa@mail.ru', 'kuciik', '$2y$10$haDTc.QmiojmVkCSMCAETOcpYc.YeqlRaT2qETUqnXFfbqvz1r3nW', 'asdkaskdasd', NULL, 'user', 0),
(26, 'admin@example.com', 'adm', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', NULL, 'user', 1),
(27, 'admin@example.com', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', NULL, 'admin', 1),
(28, 'asdasd@mail.ru', 'kuc2k', '$2y$10$6oClENUUZ7mnlMD2Pn9y4.e0XrAMquo1XliBvim2uR4GszGBgzwP.', 'asdasdasd', NULL, 'user', 0),
(29, 'asd213asd@mail.ru', 'kuciiik', '$2y$10$tGkuPcpI8B./Ri.h0U7p1.j5XgH5hpLR34TBaVnEOtwF9CZKhtGb2', 'asdasdasd', NULL, 'user', 0);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `products`
--
ALTER TABLE `products`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

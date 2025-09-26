CREATE DATABASE IF NOT EXISTS pos_sorbetes CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE pos_sorbetes;

-- Categorías
CREATE TABLE IF NOT EXISTS categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  slug VARCHAR(50) UNIQUE,        -- identificador legible (opcional)
  label VARCHAR(100) NOT NULL,
  icon VARCHAR(10) NULL
);

-- Productos
CREATE TABLE IF NOT EXISTS products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  slug VARCHAR(100) UNIQUE,       -- identificador legible (opcional)
  name VARCHAR(150) NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  category_id INT NOT NULL,
  image VARCHAR(255) NULL,
  CONSTRAINT fk_prod_cat FOREIGN KEY (category_id) REFERENCES categories(id)
);

-- Ventas (ordenes)
CREATE TABLE IF NOT EXISTS orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  date_time DATETIME NOT NULL,
  total DECIMAL(10,2) NOT NULL,
  currency VARCHAR(3) NOT NULL,
  payment_method ENUM('Tarjeta', 'Efectivo', 'PayPal') NOT NULL,
  card_type ENUM('Débito','Crédito') NULL, -- Débito | Crédito
  cash_amount DECIMAL(10,2) NULL,
  change_amount DECIMAL(10,2) NULL,
  customer_name VARCHAR(150) NULL,
  notes TEXT NULL
);

-- Items de las ordenes
CREATE TABLE IF NOT EXISTS order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  product_id INT NOT NULL,
  name VARCHAR(150) NOT NULL,
  unit_price DECIMAL(10,2) NOT NULL,
  qty INT NOT NULL,
  line_total DECIMAL(10,2) NOT NULL,
  CONSTRAINT fk_items_order FOREIGN KEY (order_id) REFERENCES orders(id),
  CONSTRAINT fk_items_product FOREIGN KEY (product_id) REFERENCES products(id)
);


-- productos

-- Categorías
INSERT INTO categories (slug, label, icon) VALUES
('helados', 'Helados', '🍨'),             -- id = 1
('cafes-calientes', 'Cafés Calientes', '☕'), -- id = 2
('cafes-helados', 'Cafés Helados', '🧋'),     -- id = 3
('crepas', 'Crepas', '🥞'),               -- id = 4
('batidos', 'Batidos', '🥤'),             -- id = 5
('paletas', 'Paletas', '🍭');             -- id = 6



-- Productos: Helados
INSERT INTO products (slug, name, price, category_id, image) VALUES
('waffle', 'Waffle', 1.50, 1, './images/waffle.png'),
('choco-waffle', 'Choco Waffle', 1.65, 1, 'images/choco-waffle.webp'),
('waffle-doble', 'Waffle Doble', 1.90, 1, 'images/waffle-dobles.jpg'),
('choco-waffle-doble', 'Choco Waffle Doble', 2.05, 1, 'images/choco-waffle-doble.webp');

-- Productos: Cafés Calientes
INSERT INTO products (slug, name, price, category_id, image) VALUES
('americano', 'Americano', 1.50, 2, 'images/cafe.webp'),
('capuchino', 'Capuchino', 1.85, 2, 'images/capuchino.webp'),
('mocachino', 'Mocachino', 2.00, 2, 'images/mocachino.webp'),
('caramel-macchiato', 'Caramel Macchiato', 2.20, 2, 'images/caramel-macchiato.webp');


-- Productos: Cafés Helados
INSERT INTO products (slug, name, price, category_id, image) VALUES
('chocolate', 'Chocolate', 2.50, 3, 'images/chocolate.jpg'),
('cookies-creme', 'Cookies and Creme', 2.50, 3, 'images/cookies-creme.webp'),
('chocogalleta', 'Choco galleta', 2.50, 3, 'images/choco-galleta.webp'),
('mocca', 'Mocca', 2.50, 3, 'images/mocca.webp'),
('caramelo', 'Caramelo', 2.50, 3, 'images/caramelo.jpg');


-- Productos: Crepas
INSERT INTO products (slug, name, price, category_id, image) VALUES
('crepe-dulce-banana', 'Dulce de Leche + Banano', 2.75, 4, 'images/crepe-dulce-banana.webp'),
('crepe-choco-banana', 'Choco Avellana + Banano', 3.00, 4, 'images/crepe-choco-banana.webp'),
('crepe-dulce-helado', 'Dulce de Leche + Helado', 3.25, 4, 'images/crepe-dulce-helado.webp'),
('crepe-choco-helado', 'Choco Avellana + Helado', 3.50, 4, 'images/crepe-choco-helado.webp'),
('crepe-dulce-banana-helado', 'Dulce de Leche Banano + Helado', 3.50, 4, 'images/crepe-dulce-banana-helado.webp'),
('crepe-choco-banana-helado', 'Choco Avellana Banano + Helado', 3.50, 4, 'images/crepe-choco-banana-helado.webp');



-- Productos: Batidos
INSERT INTO products (slug, name, price, category_id, image) VALUES
('tornado-shake', 'Tornado Shake', 2.80, 5, 'images/tornado-shake.webp'),
('mega-sundae', 'Mega Sundae', 2.90, 5, 'images/mega-sundae.webp'),
('ice-cream-soda', 'Ice Cream Soda', 2.90, 5, 'images/ice-cream-soda.webp'),
('milk-shake', 'Milk Shake', 2.90, 5, 'images/milk-shake.webp'),
('smoothie-yogurt', 'Smoothie Yogurt', 3.00, 5, 'images/smoothie-yogurt.webp');


-- Productos: Paletas
INSERT INTO products (slug, name, price, category_id, image) VALUES
('cream-pop', 'Cream Pop', 0.35, 6, 'images/cream-pop.webp'),
('unicornio', 'Unicornio', 0.50, 6, 'images/unicornio.webp'),
('mango-limon', 'Mango Limón', 0.70, 6, 'images/mango-limon.webp'),
('sandwich', 'Sandwich', 1.00, 6, 'images/sandwich.jpg'),
('goliat', 'Goliat', 1.25, 6, 'images/goliat.webp');

-- Poblar tabla orders con datos de dummy
-- ==========================
-- 1. Órdenes (orders)
-- ==========================
INSERT INTO orders (date_time, total, currency, payment_method, card_type, cash_amount, change_amount, customer_name, notes) VALUES
('2025-09-01 10:15:00', 3.70, 'USD', 'Tarjeta', 'Débito', NULL, NULL, 'Carlos Pérez', 'Compra de café'),
('2025-09-01 12:45:00', 5.20, 'USD', 'Efectivo', NULL, 10.00, 4.80, 'Ana Gómez', 'Pagó con billete de $10'),
('2025-09-02 09:30:00', 2.50, 'USD', 'PayPal', NULL, NULL, NULL, 'Luis Martínez', 'Compra en línea'),
('2025-09-02 14:10:00', 7.00, 'USD', 'Tarjeta', 'Crédito', NULL, NULL, 'Marta López', 'Consumió crepas'),
('2025-09-03 11:00:00', 1.50, 'USD', 'Efectivo', NULL, 2.00, 0.50, 'Pedro Ramírez', 'Compra rápida'),
('2025-09-03 16:20:00', 4.90, 'USD', 'Tarjeta', 'Débito', NULL, NULL, 'Julia Torres', 'Postre con café'),
('2025-09-04 10:05:00', 6.40, 'USD', 'PayPal', NULL, NULL, NULL, 'Roberto Díaz', 'Compra online'),
('2025-09-04 18:25:00', 8.25, 'USD', 'Efectivo', NULL, 10.00, 1.75, 'Claudia Herrera', 'Pagó con billete de $10'),
('2025-09-05 09:50:00', 12.00, 'USD', 'Tarjeta', 'Crédito', NULL, NULL, 'Fernando Castillo', 'Orden familiar'),
('2025-09-05 13:40:00', 2.20, 'USD', 'Tarjeta', 'Débito', NULL, NULL, 'María García', 'Compra de café'),
('2025-09-06 08:15:00', 9.50, 'USD', 'Efectivo', NULL, 20.00, 10.50, 'Jorge Sánchez', 'Pagó con billete de $20'),
('2025-09-06 17:30:00', 3.00, 'USD', 'PayPal', NULL, NULL, NULL, 'Lucía Gómez', 'Orden móvil'),
('2025-09-07 10:00:00', 4.40, 'USD', 'Tarjeta', 'Débito', NULL, NULL, 'Andrés Molina', 'Orden de capuchinos'),
('2025-09-07 15:10:00', 7.80, 'USD', 'Efectivo', NULL, 10.00, 2.20, 'Sofía Rivera', 'Pagó con billete de $10'),
('2025-09-08 09:25:00', 5.00, 'USD', 'PayPal', NULL, NULL, NULL, 'Ricardo Vargas', 'Compra en línea'),
('2025-09-08 14:50:00', 2.75, 'USD', 'Tarjeta', 'Crédito', NULL, NULL, 'Elena Ortiz', 'Crepa dulce'),
('2025-09-09 11:45:00', 6.60, 'USD', 'Efectivo', NULL, 10.00, 3.40, 'Diego Flores', 'Orden con cambio'),
('2025-09-09 18:05:00', 9.20, 'USD', 'Tarjeta', 'Débito', NULL, NULL, 'Valeria Morales', 'Orden nocturna'),
('2025-09-10 12:15:00', 3.50, 'USD', 'PayPal', NULL, NULL, NULL, 'Hugo Castillo', 'Pago online'),
('2025-09-11 10:30:00', 4.80, 'USD', 'Efectivo', NULL, 5.00, 0.20, 'Gabriela Ruiz', 'Compra con efectivo'),
('2025-09-12 09:05:00', 11.40, 'USD', 'Tarjeta', 'Crédito', NULL, NULL, 'Felipe Navarro', 'Consumo de grupo'),
('2025-09-13 16:20:00', 2.90, 'USD', 'Efectivo', NULL, 5.00, 2.10, 'Paola Cañas', 'Pago con efectivo'),
('2025-09-14 08:40:00', 7.50, 'USD', 'PayPal', NULL, NULL, NULL, 'Álvaro Ramos', 'Compra vía web'),
('2025-09-15 10:50:00', 5.60, 'USD', 'Tarjeta', 'Débito', NULL, NULL, 'Natalia Jiménez', 'Orden en mostrador'),
('2025-09-15 17:10:00', 13.25, 'USD', 'Efectivo', NULL, 20.00, 6.75, 'Manuel Cabrera', 'Pagó con billete de $20');

-- ==========================
-- 2. Items de las órdenes (order_items)
-- ==========================
-- Nota: order_id corresponde al id autoincremental generado en orders (1 a 25)

INSERT INTO order_items (order_id, product_id, name, unit_price, qty, line_total) VALUES
(1, 5, 'Mocachino', 2.00, 1, 2.00),
(1, 1, 'Waffle', 1.50, 1, 1.50),

(2, 2, 'Choco Waffle', 1.65, 2, 3.30),
(2, 9, 'Caramel Macchiato', 2.20, 1, 2.20),

(3, 11, 'Chocolate', 2.50, 1, 2.50),

(4, 17, 'Crepe Dulce + Banano', 2.75, 1, 2.75),
(4, 18, 'Crepe Choco Banana', 3.00, 1, 3.00),
(4, 2, 'Choco Waffle', 1.65, 1, 1.65),

(5, 5, 'Americano', 1.50, 1, 1.50),

(6, 20, 'Crepe Dulce + Helado', 3.25, 1, 3.25),
(6, 21, 'Crepe Choco + Helado', 3.50, 1, 3.50),

(7, 13, 'Cookies and Creme', 2.50, 2, 5.00),
(7, 14, 'Choco galleta', 2.50, 1, 2.50),

(8, 24, 'Smoothie Yogurt', 3.00, 1, 3.00),
(8, 15, 'Mocca', 2.50, 1, 2.50),
(8, 16, 'Caramelo', 2.50, 1, 2.50),

(9, 4, 'Choco Waffle Doble', 2.05, 2, 4.10),
(9, 22, 'Crepe Dulce Banana Helado', 3.50, 1, 3.50),
(9, 23, 'Crepe Choco Banana Helado', 3.50, 1, 3.50),

(10, 9, 'Caramel Macchiato', 2.20, 1, 2.20),

(11, 19, 'Crepe Dulce Helado', 3.25, 2, 6.50),
(11, 8, 'Capuchino', 1.85, 1, 1.85),
(11, 12, 'Cookies and Creme', 2.50, 1, 2.50),

(12, 3, 'Waffle Doble', 1.90, 1, 1.90),
(12, 5, 'Americano', 1.50, 1, 1.50),

(13, 6, 'Capuchino', 1.85, 2, 3.70),
(13, 7, 'Mocachino', 2.00, 1, 2.00),

(14, 21, 'Crepe Choco Helado', 3.50, 2, 7.00),

(15, 11, 'Chocolate', 2.50, 2, 5.00),

(16, 17, 'Crepe Dulce + Banano', 2.75, 1, 2.75),

(17, 24, 'Smoothie Yogurt', 3.00, 2, 6.00),
(17, 1, 'Waffle', 1.50, 1, 1.50),

(18, 22, 'Crepe Dulce Banana Helado', 3.50, 2, 7.00),
(18, 6, 'Capuchino', 1.85, 1, 1.85),

(19, 12, 'Cookies and Creme', 2.50, 1, 2.50),
(19, 8, 'Capuchino', 1.85, 1, 1.85),

(20, 5, 'Americano', 1.50, 2, 3.00),
(20, 7, 'Mocachino', 2.00, 1, 2.00),

(21, 23, 'Crepe Choco Banana Helado', 3.50, 2, 7.00),
(21, 24, 'Smoothie Yogurt', 3.00, 1, 3.00),
(21, 2, 'Choco Waffle', 1.65, 1, 1.65),

(22, 10, 'Caramel Macchiato', 2.20, 1, 2.20),
(22, 11, 'Chocolate', 2.50, 1, 2.50),

(23, 13, 'Cookies and Creme', 2.50, 1, 2.50),

(24, 15, 'Mocca', 2.50, 2, 5.00),
(24, 4, 'Choco Waffle Doble', 2.05, 1, 2.05),

(25, 19, 'Crepe Dulce Helado', 3.25, 2, 6.50),
(25, 20, 'Crepe Choco Helado', 3.50, 1, 3.50),
(25, 1, 'Waffle', 1.50, 1, 1.50);

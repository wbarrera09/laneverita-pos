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
  subtotal DECIMAL(10,2) NOT NULL,
  tax DECIMAL(10,2) NOT NULL,
  total DECIMAL(10,2) NOT NULL,
  currency VARCHAR(3) NOT NULL,
  payment_method ENUM('card','cash','paypal') NOT NULL,
  card_type ENUM('debit','credit') NULL,
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
('choco-waffle', 'Choco Waffle', 1.65, 1, './images/choco-waffle.webp'),
('waffle-doble', 'Waffle Doble', 1.90, 1, './images/waffle-dobles.jpg'),
('choco-waffle-doble', 'Choco Waffle Doble', 2.05, 1, './images/choco-waffle-doble.webp');

-- Productos: Cafés Calientes
INSERT INTO products (slug, name, price, category_id, image) VALUES
('americano', 'Americano', 1.50, 2, './images/cafe.webp'),
('capuchino', 'Capuchino', 1.85, 2, './images/capuchino.webp'),
('mocachino', 'Mocachino', 2.00, 2, './images/mocachino.webp'),
('caramel-macchiato', 'Caramel Macchiato', 2.20, 2, './images/caramel-macchiato.webp');


-- Productos: Cafés Helados
INSERT INTO products (slug, name, price, category_id, image) VALUES
('chocolate', 'Chocolate', 2.50, 3, './images/chocolate.jpg'),
('cookies-creme', 'Cookies and Creme', 2.50, 3, './images/cookies-creme.webp'),
('chocogalleta', 'Choco galleta', 2.50, 3, './images/choco-galleta.webp'),
('mocca', 'Mocca', 2.50, 3, './images/mocca.webp'),
('caramelo', 'Caramelo', 2.50, 3, './images/caramelo.jpg');


-- Productos: Crepas
INSERT INTO products (slug, name, price, category_id, image) VALUES
('crepe-dulce-banana', 'Dulce de Leche + Banano', 2.75, 4, './images/crepe-dulce-banana.webp'),
('crepe-choco-banana', 'Choco Avellana + Banano', 3.00, 4, './images/crepe-choco-banana.webp'),
('crepe-dulce-helado', 'Dulce de Leche + Helado', 3.25, 4, './images/crepe-dulce-helado.webp'),
('crepe-choco-helado', 'Choco Avellana + Helado', 3.50, 4, './images/crepe-choco-helado.webp'),
('crepe-dulce-banana-helado', 'Dulce de Leche con Banano + Helado', 3.50, 4, './images/crepe-dulce-banana-helado.webp'),
('crepe-choco-banana-helado', 'Choco Avellana con Banano + Helado', 3.50, 4, './images/crepe-choco-banana-helado.webp');



-- Productos: Batidos
INSERT INTO products (slug, name, price, category_id, image) VALUES
('tornado-shake', 'Tornado Shake', 2.80, 5, './images/tornado-shake.webp'),
('mega-sundae', 'Mega Sundae', 2.90, 5, './images/mega-sundae.webp'),
('ice-cream-soda', 'Ice Cream Soda', 2.90, 5, './images/ice-cream-soda.webp'),
('milk-shake', 'Milk Shake', 2.90, 5, './images/milk-shake.webp'),
('smoothie-yogurt', 'Smoothie Yogurt', 3.00, 5, './images/smoothie-yogurt.webp');


-- Productos: Paletas
INSERT INTO products (slug, name, price, category_id, image) VALUES
('cream-pop', 'Cream Pop', 0.35, 6, './images/cream-pop.webp'),
('unicornio', 'Unicornio', 0.50, 6, './images/unicornio.webp'),
('mango-limon', 'Mango Limón', 0.70, 6, './images/mango-limon.webp'),
('sandwich', 'Sandwich', 1.00, 6, './images/sandwich.jpg'),
('goliat', 'Goliat', 1.25, 6, './images/goliat.webp');


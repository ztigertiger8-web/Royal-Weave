-- Create database
CREATE DATABASE IF NOT EXISTS royal_wave
DEFAULT CHARACTER SET utf8mb4
DEFAULT COLLATE utf8mb4_general_ci;

USE royal_wave;

-- Admin accounts table
CREATE TABLE IF NOT EXISTS admin (
  admin_id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  email VARCHAR(120) NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  full_name VARCHAR(120),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Customer accounts table
CREATE TABLE IF NOT EXISTS customer (
  customer_id INT AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(120) NOT NULL,
  email VARCHAR(120) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  phone VARCHAR(30),
  address VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  last_login_at DATETIME NULL,
  last_logout_at DATETIME NULL,
  last_activity_at DATETIME NULL
);

-- Product categories table supports main and sub categories
CREATE TABLE IF NOT EXISTS category (
  category_id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  parent_category_id INT NULL,
  UNIQUE (name, parent_category_id),
  CONSTRAINT fk_category_parent
  FOREIGN KEY (parent_category_id) REFERENCES category(category_id)
  ON UPDATE CASCADE ON DELETE CASCADE
);

-- Products table stores basic product info and main image filename
CREATE TABLE IF NOT EXISTS product (
  product_id INT AUTO_INCREMENT PRIMARY KEY,
  category_id INT NOT NULL,
  name VARCHAR(180) NOT NULL UNIQUE,
  description TEXT,
  product_kind ENUM('fabric','shemagh') NOT NULL,
  main_image_filename VARCHAR(200) NOT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  created_by_admin_id INT NULL,
  CONSTRAINT fk_product_category
  FOREIGN KEY (category_id) REFERENCES category(category_id)
  ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_product_admin
  FOREIGN KEY (created_by_admin_id) REFERENCES admin(admin_id)
  ON UPDATE CASCADE ON DELETE SET NULL
);

-- Product variants table stores price stock and image per variant
CREATE TABLE IF NOT EXISTS product_variant (
  variant_id INT AUTO_INCREMENT PRIMARY KEY,
  product_id INT NOT NULL,
  sku VARCHAR(64) NOT NULL UNIQUE,
  price DECIMAL(10,2) NOT NULL CHECK (price >= 0),
  stock_qty INT NOT NULL DEFAULT 0 CHECK (stock_qty >= 0),
  color VARCHAR(60) NULL,
  size VARCHAR(20) NULL,
  origin_country VARCHAR(60) NULL,
  weave_type VARCHAR(60) NULL,
  season VARCHAR(20) NULL,
  water_resistant ENUM('Yes','No') NULL,
  image_filename VARCHAR(200) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_variant_product
  FOREIGN KEY (product_id) REFERENCES product(product_id)
  ON UPDATE CASCADE ON DELETE CASCADE
);

-- Orders table is_cart 1 means current shopping cart
CREATE TABLE IF NOT EXISTS orders (
  order_id INT AUTO_INCREMENT PRIMARY KEY,
  customer_id INT NOT NULL,
  is_cart TINYINT(1) NOT NULL DEFAULT 1,
  order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  status ENUM('pending','paid','shipped','cancelled') NOT NULL DEFAULT 'pending',
  payment_method ENUM('cash','card') NOT NULL DEFAULT 'cash',
  payment_status ENUM('pending','paid','failed') NOT NULL DEFAULT 'pending',
  total_amount DECIMAL(10,2) NOT NULL DEFAULT 0 CHECK (total_amount >= 0),
  CONSTRAINT fk_orders_customer
  FOREIGN KEY (customer_id) REFERENCES customer(customer_id)
  ON UPDATE CASCADE ON DELETE RESTRICT
);

-- Order items table stores items inside each order
CREATE TABLE IF NOT EXISTS order_items (
  order_item_id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  variant_id INT NOT NULL,
  qty INT NOT NULL CHECK (qty > 0),
  unit_price DECIMAL(10,2) NOT NULL CHECK (unit_price >= 0),
  UNIQUE KEY uq_order_variant (order_id, variant_id),
  CONSTRAINT fk_oi_order
  FOREIGN KEY (order_id) REFERENCES orders(order_id)
  ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_oi_variant
  FOREIGN KEY (variant_id) REFERENCES product_variant(variant_id)
  ON UPDATE CASCADE ON DELETE RESTRICT
);

-- User sessions table tracks login and logout
CREATE TABLE IF NOT EXISTS user_session (
  session_id INT AUTO_INCREMENT PRIMARY KEY,
  customer_id INT NOT NULL,
  login_time DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  logout_time DATETIME NULL,
  session_status ENUM('active','logged_out','expired') NOT NULL DEFAULT 'active',
  ip_address VARCHAR(45) NULL,
  user_agent VARCHAR(255) NULL,
  CONSTRAINT fk_user_session_customer
  FOREIGN KEY (customer_id) REFERENCES customer(customer_id)
  ON UPDATE CASCADE ON DELETE CASCADE
);

-- Activity log table tracks user actions
CREATE TABLE IF NOT EXISTS activity_log (
  activity_id INT AUTO_INCREMENT PRIMARY KEY,
  customer_id INT NOT NULL,
  session_id INT NULL,
  activity_type ENUM(
    'login',
    'logout',
    'view_product',
    'add_to_cart',
    'remove_from_cart',
    'update_cart_qty',
    'place_order',
    'cancel_order',
    'update_profile',
    'change_password'
  ) NOT NULL,
  activity_details VARCHAR(255) NULL,
  order_id INT NULL,
  variant_id INT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_activity_customer
  FOREIGN KEY (customer_id) REFERENCES customer(customer_id)
  ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_activity_session
  FOREIGN KEY (session_id) REFERENCES user_session(session_id)
  ON UPDATE CASCADE ON DELETE SET NULL,
  CONSTRAINT fk_activity_order
  FOREIGN KEY (order_id) REFERENCES orders(order_id)
  ON UPDATE CASCADE ON DELETE SET NULL,
  CONSTRAINT fk_activity_variant
  FOREIGN KEY (variant_id) REFERENCES product_variant(variant_id)
  ON UPDATE CASCADE ON DELETE SET NULL
);

-- Indexes for performance
CREATE INDEX idx_user_session_customer_id ON user_session(customer_id);
CREATE INDEX idx_user_session_status ON user_session(session_status);
CREATE INDEX idx_activity_customer_id ON activity_log(customer_id);
CREATE INDEX idx_activity_session_id ON activity_log(session_id);
CREATE INDEX idx_activity_order_id ON activity_log(order_id);
CREATE INDEX idx_activity_variant_id ON activity_log(variant_id);
CREATE INDEX idx_activity_type ON activity_log(activity_type);
CREATE INDEX idx_activity_created_at ON activity_log(created_at);

-- Insert default admin
INSERT INTO admin (username, email, password_hash, full_name)
VALUES 
  ('admin1', 'admin1@gmail.com', '12345678', 'Tamim Al-Suhaibani'),
  ('admin2', 'admin2@gmail.com', '123456789', 'Abdulmohsen'),
  ('admin3', 'admin3@gmail.com', '1234567890', 'zyad')
ON DUPLICATE KEY UPDATE full_name=VALUES(full_name), email=VALUES(email);

-- Insert sample customer
INSERT INTO customer (full_name, email, password_hash, phone, address)
VALUES ('Hamad Example', 'hamad@example.com', 'hashed_customer_password_here', '0500000000', 'Dammam, Saudi Arabia')
ON DUPLICATE KEY UPDATE full_name=VALUES(full_name), phone=VALUES(phone), address=VALUES(address);

-- Insert main categories
INSERT IGNORE INTO category (name, parent_category_id)
VALUES ('الأقمشة', NULL), ('الأشمغة', NULL);

-- Insert sample product
INSERT INTO product (category_id, name, description, product_kind, is_active, main_image_filename, created_by_admin_id)
VALUES (1, 'قماش بريمواميس صيفي', 'Premium Japanese cotton fabric.', 'fabric', 1, 'primoamis_main.jpg', 1)
ON DUPLICATE KEY UPDATE description=VALUES(description), main_image_filename=VALUES(main_image_filename), is_active=VALUES(is_active);

-- Insert sample variant
INSERT INTO product_variant (product_id, sku, price, stock_qty, color, origin_country, image_filename)
VALUES (1, 'FAB-PRM-WHT', 250.00, 50, 'أبيض', 'اليابان', 'primoamis_white.jpg')
ON DUPLICATE KEY UPDATE price=VALUES(price), stock_qty=VALUES(stock_qty), image_filename=VALUES(image_filename);

-- Create initial cart for the sample customer
INSERT IGNORE INTO orders (customer_id, is_cart, status, payment_method, payment_status, total_amount)
VALUES (1, 1, 'pending', 'cash', 'pending', 0.00);
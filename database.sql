-- Create database
CREATE DATABASE IF NOT EXISTS ecommerce_store;
USE ecommerce_store;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    address TEXT,
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Categories table
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    description TEXT,
    image VARCHAR(255),
    parent_id INT,
    FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Products table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,CREATE DATABASE ecommerce_store;
USE ecommerce_store;
source database.sql;
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    discount_price DECIMAL(10, 2),
    category_id INT,
    stock INT NOT NULL DEFAULT 0,
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Orders table
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    shipping_address TEXT NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Order items table
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Cart table
CREATE TABLE IF NOT EXISTS cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY (user_id, product_id)
);

-- Insert sample categories
INSERT INTO categories (name, description, image) VALUES
('Dress', 'All types of clothing items', 'dress_category.jpg'),
('Food', 'Food and grocery items', 'food_category.jpg'),
('Electronics', 'Electronic devices and accessories', 'electronics_category.jpg');

-- Insert subcategories
INSERT INTO categories (name, description, image, parent_id) VALUES
('Men\'s Shirt', 'Shirts for men', 'mens_shirt.jpg', 1),
('Women\'s Kurti', 'Kurtis for women', 'womens_kurti.jpg', 1),
('Kidswear', 'Clothing for kids', 'kidswear.jpg', 1),
('Snacks', 'Packaged snacks', 'snacks.jpg', 2),
('Beverages', 'Drinks and beverages', 'beverages.jpg', 2),
('Grocery', 'Essential grocery items', 'grocery.jpg', 2),
('Mobiles', 'Smartphones and mobile phones', 'mobiles.jpg', 3),
('Laptops', 'Laptops and notebooks', 'laptops.jpg', 3),
('Accessories', 'Electronic accessories', 'accessories.jpg', 3);

-- Insert sample products
INSERT INTO products (name, description, price, discount_price, category_id, stock, image) VALUES
-- Men's Shirts
('Casual Blue Shirt', 'Comfortable cotton casual shirt for men', 999.00, 799.00, 4, 50, 'blue_shirt.jpg'),
('Formal White Shirt', 'Crisp formal shirt for office wear', 1299.00, 999.00, 4, 30, 'white_shirt.jpg'),
('Checked Shirt', 'Stylish checked pattern shirt', 899.00, 699.00, 4, 45, 'checked_shirt.jpg'),

-- Women's Kurti
('Floral Print Kurti', 'Beautiful floral print kurti for women', 1499.00, 1199.00, 5, 40, 'floral_kurti.jpg'),
('Embroidered Kurti', 'Hand embroidered designer kurti', 1999.00, 1599.00, 5, 25, 'embroidered_kurti.jpg'),
('Cotton Kurti Set', 'Comfortable cotton kurti with palazzo', 2499.00, 1999.00, 5, 20, 'kurti_set.jpg'),

-- Kidswear
('Boys T-shirt', 'Colorful printed t-shirt for boys', 599.00, 499.00, 6, 60, 'boys_tshirt.jpg'),
('Girls Frock', 'Pretty frock for little girls', 899.00, 699.00, 6, 35, 'girls_frock.jpg'),
('Kids Jeans', 'Durable jeans for children', 799.00, 649.00, 6, 40, 'kids_jeans.jpg'),

-- Snacks
('Lays Classic', 'Classic salted potato chips', 20.00, NULL, 7, 100, 'lays.jpg'),
('Kurkure', 'Masala flavored crunchy snack', 20.00, NULL, 7, 100, 'kurkure.jpg'),
('Biscuits Pack', 'Assorted biscuits pack', 30.00, 25.00, 7, 80, 'biscuits.jpg'),

-- Beverages
('Pepsi 2L', '2 liter bottle of Pepsi', 99.00, NULL, 8, 50, 'pepsi.jpg'),
('Sprite 1L', '1 liter bottle of Sprite', 70.00, NULL, 8, 50, 'sprite.jpg'),
('Fruit Juice Pack', 'Natural mixed fruit juice', 120.00, 99.00, 8, 30, 'juice.jpg'),

-- Grocery
('Basmati Rice 5kg', 'Premium quality basmati rice', 399.00, 349.00, 9, 25, 'rice.jpg'),
('Toor Dal 1kg', 'High quality toor dal', 150.00, 129.00, 9, 40, 'dal.jpg'),
('Cooking Oil 1L', 'Refined sunflower cooking oil', 199.00, 179.00, 9, 35, 'oil.jpg'),

-- Mobiles
('Smartphone X', 'Latest model with 8GB RAM and 128GB storage', 24999.00, 22999.00, 10, 15, 'smartphone_x.jpg'),
('Budget Phone', 'Affordable smartphone with good features', 9999.00, 8999.00, 10, 25, 'budget_phone.jpg'),
('Premium Phone Pro', 'High-end smartphone with advanced camera', 49999.00, 45999.00, 10, 10, 'premium_phone.jpg'),

-- Laptops
('Business Laptop', 'Reliable laptop for business use', 45999.00, 42999.00, 11, 10, 'business_laptop.jpg'),
('Gaming Laptop', 'High performance gaming laptop', 79999.00, 74999.00, 11, 8, 'gaming_laptop.jpg'),
('Student Notebook', 'Affordable laptop for students', 29999.00, 27999.00, 11, 20, 'student_laptop.jpg'),

-- Accessories
('boAt Headphones', 'Wireless over-ear headphones', 1999.00, 1499.00, 12, 30, 'boat_headphones.jpg'),
('JBL Earbuds', 'True wireless earbuds with noise cancellation', 2999.00, 2499.00, 12, 25, 'jbl_earbuds.jpg'),
('Power Bank', '10000mAh fast charging power bank', 1499.00, 1299.00, 12, 40, 'power_bank.jpg');
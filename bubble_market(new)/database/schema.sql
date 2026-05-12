-- ============================================
-- Bubble Campus Marketplace - Database Schema
-- CS381 Phase 3
-- Students: Jana Ahmed Darandari & Dana Majed Aljehani
-- ============================================

CREATE DATABASE IF NOT EXISTS bubble_market;
USE bubble_market;

-- ============================================
-- TABLE 1: users
-- ============================================
CREATE TABLE IF NOT EXISTS users (
    user_id     INT AUTO_INCREMENT PRIMARY KEY,
    email       VARCHAR(100) NOT NULL UNIQUE,
    password    VARCHAR(255) NOT NULL,
    role        ENUM('student', 'admin') DEFAULT 'student',
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================
-- TABLE 2: products
-- ============================================
CREATE TABLE IF NOT EXISTS products (
    product_id  INT AUTO_INCREMENT PRIMARY KEY,
    seller_id   INT NOT NULL,
    title       VARCHAR(150) NOT NULL,
    category    VARCHAR(100),
    price       DECIMAL(10,2) NOT NULL,
    description TEXT,
    status      ENUM('available', 'sold') DEFAULT 'available',
    item_image  VARCHAR(255),
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (seller_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- ============================================
-- TABLE 3: messages
-- ============================================
CREATE TABLE IF NOT EXISTS messages (
    message_id   INT AUTO_INCREMENT PRIMARY KEY,
    sender_id    INT NOT NULL,
    receiver_id  INT NOT NULL,
    item_id      INT NOT NULL,
    message_text TEXT NOT NULL,
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id)   REFERENCES users(user_id)    ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(user_id)    ON DELETE CASCADE,
    FOREIGN KEY (item_id)     REFERENCES products(product_id) ON DELETE CASCADE
);

-- ============================================
-- SAMPLE DATA — users (password = "password" hashed)
-- Note: In real use, passwords are hashed with password_hash()
-- For testing, we store plain here then PHP will hash on register
-- ============================================
INSERT INTO users (email, password, role) VALUES
('admin@campus.edu',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('student@campus.edu',  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student'),
('sara@campus.edu',     '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student'),
('noura@campus.edu',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student'),
('hana@campus.edu',     '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student'),
('reem@campus.edu',     '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student'),
('dana@campus.edu',     '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student'),
('lina@campus.edu',     '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student'),
('maha@campus.edu',     '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student'),
('jana@campus.edu',     '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student');
-- All passwords above = "password" (hashed with bcrypt)

-- ============================================
-- SAMPLE DATA — products (minimum 10 records)
-- ============================================
INSERT INTO products (seller_id, title, category, price, description, status, item_image) VALUES
(2, 'Calculus Textbook',     'Books',     120.00, 'Barely used, 8th edition. All pages intact.', 'available', ''),
(3, 'iPad Air 5th Gen',      'Tablets',  1800.00, 'Comes with original case and charger.', 'available', ''),
(4, 'Sony Headphones',       'Audio',     450.00, 'WH-1000XM4, noise cancelling. Like new.', 'available', ''),
(5, 'Engineering Chair',     'Furniture', 300.00, 'Ergonomic chair, used for 1 semester.', 'available', ''),
(6, 'Physics Textbook',      'Books',      80.00, 'Halliday & Resnick, Volume 1 & 2.', 'sold',      ''),
(7, 'Samsung Galaxy Tab S8', 'Tablets',  1200.00, 'Includes S-pen and keyboard cover.', 'available', ''),
(8, 'JBL Speaker',           'Audio',     200.00, 'Portable, Bluetooth. Good battery life.', 'available', ''),
(9, 'Study Desk',            'Furniture', 350.00, 'IKEA desk, white, fits small spaces.',  'available', ''),
(10,'Python Programming Book','Books',     95.00, 'Latest edition with online exercises.',  'available', ''),
(2, 'AirPods Pro',           'Audio',     650.00, 'Gen 2, with charging case.',             'available', ''),
(3, 'Chemistry Textbook',    'Books',      70.00, 'Atkins Chemistry, 10th edition.',        'sold',      ''),
(4, 'Laptop Stand',          'Furniture', 120.00, 'Adjustable aluminum stand.',             'available', '');

-- ============================================
-- SAMPLE DATA — messages (minimum 10 records)
-- ============================================
INSERT INTO messages (sender_id, receiver_id, item_id, message_text) VALUES
(3,  2,  1,  'Is the Calculus book still available?'),
(4,  2,  1,  'Can you give a small discount on the textbook?'),
(5,  3,  2,  'Is the iPad Air in good condition?'),
(6,  3,  2,  'Does the iPad come with the original box?'),
(7,  4,  3,  'Are the headphones still available?'),
(8,  5,  4,  'Can I see the chair before buying?'),
(9,  7,  6,  'I need the Samsung Tab, is it negotiable?'),
(10, 8,  7,  'Does the JBL speaker work with iOS?'),
(2,  9,  8,  'What are the desk dimensions?'),
(3,  10, 9,  'Is the Python book the 2024 edition?');

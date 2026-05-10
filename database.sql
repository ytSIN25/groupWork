-- ============================================
-- LUMIÈRE Cinema - Full Database Schema
-- ============================================

CREATE DATABASE IF NOT EXISTS lumiere_cinema;
USE lumiere_cinema;

-- -------------------------------------------------
-- Users: stores every registered patron / admin
-- -------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    user_id     INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(255)   NOT NULL,
    email       VARCHAR(255)   NOT NULL UNIQUE,
    password    VARCHAR(255)   NOT NULL,
    role        ENUM('patron','admin') NOT NULL DEFAULT 'patron',
    tier        VARCHAR(100)   DEFAULT 'Bronze Reel Member',
    avatar      VARCHAR(500)   DEFAULT NULL,
    created_at  TIMESTAMP      DEFAULT CURRENT_TIMESTAMP
);

-- -------------------------------------------------
-- Insert a default admin
-- Password for admin: "admin"
-- Patrons must sign up through the registration form
-- ------------------------------------------------
INSERT INTO users (user_id, name, email, password, role, tier, avatar)
SELECT
  1,
  'Arthur Pendelton',
  'arthur@lumiere.com',
  '$2y$10$luKg30NFH4eYDV0H3XKPYOkqtrA0c0ADCqkzBBNHTXdJZw0Qk.kfu',
  'admin',
  'Chief Operator',
  'https://api.dicebear.com/7.x/notionists/svg?seed=Arthur'
WHERE NOT EXISTS (
  SELECT 1 FROM users WHERE email = 'arthur@lumiere.com'
);

-- -------------------------------------------------
-- Movies
-- -------------------------------------------------
CREATE TABLE IF NOT EXISTS movies (
    movie_id     INT AUTO_INCREMENT PRIMARY KEY,
    movie_name   VARCHAR(255) NOT NULL,
    user_id      INT NOT NULL,
    director     VARCHAR(255),
    genre        ENUM('Action', 'Adventure', 'Comedy', 'Crime', 'Drama', 'Fantasy', 'Historical', 'Horror', 'Musical', 'Romance', 'Sci-Fi', 'Thriller'),
    release_year INT,
    starring     TEXT,
    description  TEXT,
    poster_path  VARCHAR(255),
    duration     INT,
    start_date   DATE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- -------------------------------------------------
-- Showtimes (multiple slots per movie)
-- -------------------------------------------------
CREATE TABLE IF NOT EXISTS showtimes (
    showtime_id       INT AUTO_INCREMENT PRIMARY KEY,
    movie_id          INT NOT NULL,
    auditorium_number INT NOT NULL,
    start_time        TIME NOT NULL,
    FOREIGN KEY (movie_id) REFERENCES movies(movie_id) ON DELETE CASCADE
);

-- -------------------------------------------------
-- Insert 3 Showtimes per Movie (Slot templates)
-- -------------------------------------------------
INSERT INTO showtimes (movie_id, auditorium_number, start_time) VALUES
-- Movie 1
(1, 1, '14:00:00'), (1, 1, '17:30:00'), (1, 1, '21:00:00'),
-- Movie 2
(2, 2, '14:00:00'), (2, 2, '17:30:00'), (2, 2, '21:00:00'),
-- Movie 3
(3, 1, '14:30:00'), (3, 1, '18:00:00'), (3, 1, '21:30:00'),
-- Movie 4
(4, 2, '14:30:00'), (4, 2, '18:00:00'), (4, 2, '21:30:00');


-- -------------------------------------------------
-- Promotions
-- -------------------------------------------------
CREATE TABLE IF NOT EXISTS promotions (
    promotion_id   INT AUTO_INCREMENT PRIMARY KEY,
    user_id        INT NOT NULL,
    discount_value DECIMAL(5, 2) NOT NULL,
    promo_code     VARCHAR(50) NOT NULL UNIQUE,
    description    TEXT,
    minimum_spend  DECIMAL(10, 2) DEFAULT 0.00,
    is_active      TINYINT(1) DEFAULT 1,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- -------------------------------------------------
-- Orders
-- -------------------------------------------------
CREATE TABLE IF NOT EXISTS orders (
    order_id      INT AUTO_INCREMENT PRIMARY KEY,
    user_id       INT NOT NULL,
    movie_id      INT NOT NULL,
    promotion_id  INT,
    show_date     DATE NOT NULL,
    show_time     TIME NOT NULL,
    order_date    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    seats         TEXT NOT NULL,
    num_seats     INT NOT NULL,
    total_price   DECIMAL(10, 2) NOT NULL,
    cc_number     VARCHAR(20) DEFAULT NULL,
    cc_expiry     VARCHAR(10) DEFAULT NULL,
    cc_cvc        VARCHAR(10) DEFAULT NULL,
    FOREIGN KEY (user_id)       REFERENCES users(user_id)       ON DELETE CASCADE,
    FOREIGN KEY (movie_id)      REFERENCES movies(movie_id)     ON DELETE CASCADE,
    FOREIGN KEY (promotion_id)  REFERENCES promotions(promotion_id) ON DELETE SET NULL
);

-- -------------------------------------------------
-- Ratings
-- -------------------------------------------------
CREATE TABLE IF NOT EXISTS ratings (
    rating_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id   INT NOT NULL,
    movie_id  INT NOT NULL,
    content   TEXT,
    star_num  INT CHECK (star_num >= 1 AND star_num <= 5),
    FOREIGN KEY (user_id)  REFERENCES users(user_id)  ON DELETE CASCADE,
    FOREIGN KEY (movie_id) REFERENCES movies(movie_id) ON DELETE CASCADE
);

-- -------------------------------------------------
-- User Preferences
-- -------------------------------------------------
CREATE TABLE IF NOT EXISTS user_preferences (
    user_id           INT PRIMARY KEY,
    preferred_seating VARCHAR(100) DEFAULT 'The Circle (Balcony)',
    preferred_snack   VARCHAR(255) DEFAULT '',
    preferred_genre   VARCHAR(100) DEFAULT '',
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);
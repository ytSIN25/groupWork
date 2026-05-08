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
    price        DECIMAL(10, 2),
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
    show_date         DATE NOT NULL,
    start_time        TIME NOT NULL,
    FOREIGN KEY (movie_id) REFERENCES movies(movie_id) ON DELETE CASCADE
);

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
    showtime_id   INT NOT NULL,
    promotion_id  INT,
    order_date    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    seats         TEXT NOT NULL,
    num_seats     INT NOT NULL,
    total_price   DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (user_id)       REFERENCES users(user_id)       ON DELETE CASCADE,
    FOREIGN KEY (showtime_id)   REFERENCES showtimes(showtime_id) ON DELETE CASCADE,
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
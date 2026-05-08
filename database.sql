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
    genre        VARCHAR(100),
    release_year INT,
    starring     TEXT,
    description  TEXT,
    poster_path  VARCHAR(255),
    duration     INT,
    price        DECIMAL(10, 2),
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

-- Insert Sample Movies
INSERT INTO movies (movie_name, user_id, director, genre, release_year, starring, description, poster_path, duration, price) 
VALUES 
('Oppenheimer', 1, 'Christopher Nolan', 'Biography, Drama', 2023, 'Cillian Murphy, Emily Blunt', 'The story of American scientist J. Robert Oppenheimer and his role in the development of the atomic bomb.', 'assets/images/poster-oppenheimer.png', 180, 12.00),

('Dune: Part Two', 1, 'Denis Villeneuve', 'Sci-Fi, Adventure', 2024, 'Timothée Chalamet, Zendaya', 'Paul Atreides unites with Chani and the Fremen while on a warpath of revenge against the conspirators who destroyed his family.', 'assets/images/poster-dune.png', 166, 15.00),

('Nosferatu', 1, 'Robert Eggers', 'Horror, Fantasy', 2024, 'Bill Skarsgård, Nicholas Hoult', 'A gothic tale of obsession between a haunted young woman and the terrifying vampire infatuated with her.', 'assets/images/poster-nosferatu.png', 125, 12.50),

('Interstellar', 1, 'Christopher Nolan', 'Sci-Fi, Drama', 2014, 'Matthew McConaughey, Anne Hathaway', 'A team of explorers travel through a wormhole in space in an attempt to ensure humanity''s survival.', 'assets/images/poster-interstellar.png', 169, 10.00),

('Blade Runner 2049', 1, 'Denis Villeneuve', 'Sci-Fi, Mystery', 2017, 'Ryan Gosling, Harrison Ford', 'A young blade runner''s discovery of a long-buried secret leads him to track down former blade runner Rick Deckard.', 'assets/images/poster-bladerunner.png', 164, 15.00);

-- Insert sample Showtimes for these movies
INSERT INTO showtimes (movie_id, auditorium_number, show_date, start_time)
VALUES 
(1, 1, CURDATE(), '14:00:00'),
(1, 1, CURDATE(), '19:30:00'),
(2, 2, CURDATE(), '15:00:00'),
(3, 3, CURDATE(), '21:00:00'),
(4, 4, CURDATE(), '13:00:00');

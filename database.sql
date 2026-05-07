CREATE DATABASE IF NOT EXISTS Coursework2;
USE Coursework2;

-- All user and company credentials
CREATE TABLE IF NOT EXISTS Users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    user_type ENUM('user', 'organiser') NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone_number VARCHAR(20) UNIQUE,
    create_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Movies (General Metadata)
CREATE TABLE IF NOT EXISTS Movies (
    movie_id INT AUTO_INCREMENT PRIMARY KEY,
    movie_name VARCHAR(255) NOT NULL,
    user_id INT NOT NULL, -- The organiser who added it
    director VARCHAR(255),
    genre VARCHAR(100),
    release_year INT,
    starring TEXT,
    description TEXT,
    poster_path VARCHAR(255), -- Fixed missing comma
    duration INT, 
    price DECIMAL(10, 2),
    FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE
);

-- New Table: Showtimes (To handle multiple slots properly)
CREATE TABLE IF NOT EXISTS Showtimes (
    showtime_id INT AUTO_INCREMENT PRIMARY KEY,
    movie_id INT NOT NULL,
    auditorium_number INT NOT NULL,
    show_date DATE NOT NULL,
    start_time TIME NOT NULL,
    FOREIGN KEY (movie_id) REFERENCES Movies(movie_id) ON DELETE CASCADE
);

-- Promotions
CREATE TABLE IF NOT EXISTS Promotions (
    promotion_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    discount_value DECIMAL(5, 2) NOT NULL,
    promo_code VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    minimum_spend DECIMAL(10, 2) DEFAULT 0.00,
    FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE
);

-- Orders
CREATE TABLE IF NOT EXISTS Orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    showtime_id INT NOT NULL, -- Link to specific showtime instead of movie
    promotion_id INT,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    seats TEXT NOT NULL, 
    num_seats INT NOT NULL,
    total_price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (showtime_id) REFERENCES Showtimes(showtime_id) ON DELETE CASCADE,
    FOREIGN KEY (promotion_id) REFERENCES Promotions(promotion_id) ON DELETE SET NULL
);

-- Ratings
CREATE TABLE IF NOT EXISTS Ratings (
    rating_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    movie_id INT NOT NULL,
    content TEXT,
    star_num INT CHECK (star_num >= 1 AND star_num <= 5),
    FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (movie_id) REFERENCES Movies(movie_id) ON DELETE CASCADE
);

USE Coursework2;
-- 1. Add Users 
INSERT INTO Users (user_type, first_name, last_name, password, email, phone_number) VALUES
('organiser', 'Alice', 'Admin', 'hashed_admin_pass', 'admin@cinema.com', '555-0101'),
('user', 'Bob', 'Customer', 'hashed_user_pass', 'bob@example.com', '555-0102');

-- 2. Add 5 Movies
INSERT INTO Movies (movie_name, user_id, director, genre, release_year, starring, description, duration, price) VALUES
('Interstellar', 1, 'Christopher Nolan', 'Sci-Fi', 2014, 'Matthew McConaughey, Anne Hathaway', 'A team of explorers travel through a wormhole in space.', 169, 12.50),
('The Dark Knight', 1, 'Christopher Nolan', 'Action', 2008, 'Christian Bale, Heath Ledger', 'Batman faces the Joker in Gotham City.', 152, 10.00),
('Inception', 1, 'Christopher Nolan', 'Sci-Fi', 2010, 'Leonardo DiCaprio', 'A thief steals secrets through the use of dream-sharing technology.', 148, 11.00),
('Parasite', 1, 'Bong Joon-ho', 'Thriller', 2019, 'Song Kang-ho, Lee Sun-kyun', 'Greed and class discrimination threaten a relationship.', 132, 12.00),
('Spirited Away', 1, 'Hayao Miyazaki', 'Animation', 2001, 'Rumi Hiiragi', 'A young girl wanders into a world ruled by gods and spirits.', 125, 9.50);

-- 3. Add Showtimes
INSERT INTO Showtimes (movie_id, auditorium_number, show_date, start_time) VALUES
(1, 1, '2026-05-10', '19:00:00'),
(2, 2, '2026-05-10', '21:30:00');

-- 4. Add 2 Orders from the User
INSERT INTO Orders (user_id, showtime_id, seats, num_seats, total_price) VALUES
(2, 1, 'A1, A2', 2, 25.00), -- Order for Interstellar
(2, 2, 'C5', 1, 10.00);     -- Order for The Dark Knight
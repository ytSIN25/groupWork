-- ============================================
-- LUMIÈRE Cinema - Dummy Data Population Script
-- ============================================

-- Disable foreign key checks to allow clearing tables with dependencies
SET FOREIGN_KEY_CHECKS = 0;
DELETE FROM orders;
DELETE FROM showtimes;
DELETE FROM promotions;
DELETE FROM movies;
DELETE FROM ratings;

-- Resetting Auto-Increments to ensure IDs start from 1
ALTER TABLE orders AUTO_INCREMENT = 1;
ALTER TABLE showtimes AUTO_INCREMENT = 1;
ALTER TABLE promotions AUTO_INCREMENT = 1;
ALTER TABLE movies AUTO_INCREMENT = 1;

-- Clean up users
DELETE FROM users WHERE user_id > 1;
ALTER TABLE users AUTO_INCREMENT = 2;

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;

-- 1. Insert Sample Movies (Owned by Admin ID 1)
INSERT INTO movies (movie_id, movie_name, user_id, director, genre, release_year, starring, description, poster_path, duration, start_date) 
VALUES 
(1, 'Oppenheimer', 1, 'Christopher Nolan', 'Historical', 2023, 'Cillian Murphy', 'Atomic bomb biography.', 'assets/images/poster-oppenheimer.png', 180, '2026-05-05'),
(2, 'Dune: Part Two', 1, 'Denis Villeneuve', 'Sci-Fi', 2024, 'Zendaya', 'Desert epic.', 'assets/images/poster-dune.png', 166, '2026-05-08'),
(3, 'Nosferatu', 1, 'Robert Eggers', 'Horror', 2024, 'Bill Skarsgård', 'Gothic horror.', 'assets/images/poster-nosferatu.png', 125, '2026-05-11'),
(4, 'Interstellar', 1, 'Christopher Nolan', 'Sci-Fi', 2014, 'Matthew McConaughey', 'Space travel.', 'assets/images/poster-interstellar.png', 169, '2026-05-03'),
(5, 'Blade Runner 2049', 1, 'Denis Villeneuve', 'Sci-Fi', 2017, 'Ryan Gosling', 'Neon detective.', 'assets/images/poster-bladerunner.png', 164, '2026-05-02');

-- 2. Insert Showtimes
INSERT INTO showtimes (movie_id, auditorium_number, start_time)
VALUES 
(1, 1, '14:00:00'), (1, 1, '17:30:00'), (1, 1, '21:00:00'),
(2, 2, '14:00:00'), (2, 2, '17:30:00'), (2, 2, '21:00:00'),
(3, 1, '14:30:00'), (3, 1, '18:00:00'), (3, 1, '21:30:00'),
(4, 2, '14:30:00'), (4, 2, '18:00:00'), (4, 2, '21:30:00'),
(5, 3, '14:30:00'), (5, 3, '18:00:00'), (5, 3, '21:30:00');

-- 3. Insert 10 Users (Patrons)
INSERT INTO users (user_id, name, email, password, role, tier, avatar) VALUES
(2, 'Julian Vane', 'julian@patron.com', '$2y$12$eSUJY1n7u219LnGACRK3aer.EnLu8Ik643haSD5WPQkAowOL5D6sS', 'patron', 'Gold Reel Member', 'https://api.dicebear.com/7.x/notionists/svg?seed=Julian'),
(3, 'Clara Oswald', 'clara@patron.com', '$2y$12$eSUJY1n7u219LnGACRK3aer.EnLu8Ik643haSD5WPQkAowOL5D6sS', 'patron', 'Silver Reel Member', 'https://api.dicebear.com/7.x/notionists/svg?seed=Clara'),
(4, 'Danny Pink', 'danny@patron.com', '$2y$12$eSUJY1n7u219LnGACRK3aer.EnLu8Ik643haSD5WPQkAowOL5D6sS', 'patron', 'Bronze Reel Member', 'https://api.dicebear.com/7.x/notionists/svg?seed=Danny'),
(5, 'Rose Tyler', 'rose@patron.com', '$2y$12$eSUJY1n7u219LnGACRK3aer.EnLu8Ik643haSD5WPQkAowOL5D6sS', 'patron', 'Gold Reel Member', 'https://api.dicebear.com/7.x/notionists/svg?seed=Rose'),
(6, 'Martha Jones', 'martha@patron.com', '$2y$12$eSUJY1n7u219LnGACRK3aer.EnLu8Ik643haSD5WPQkAowOL5D6sS', 'patron', 'Silver Reel Member', 'https://api.dicebear.com/7.x/notionists/svg?seed=Martha'),
(7, 'Donna Noble', 'donna@patron.com', '$2y$12$eSUJY1n7u219LnGACRK3aer.EnLu8Ik643haSD5WPQkAowOL5D6sS', 'patron', 'Gold Reel Member', 'https://api.dicebear.com/7.x/notionists/svg?seed=Donna'),
(8, 'Amy Pond', 'amy@patron.com', '$2y$12$eSUJY1n7u219LnGACRK3aer.EnLu8Ik643haSD5WPQkAowOL5D6sS', 'patron', 'Bronze Reel Member', 'https://api.dicebear.com/7.x/notionists/svg?seed=Amy'),
(9, 'Rory Williams', 'rory@patron.com', '$2y$12$eSUJY1n7u219LnGACRK3aer.EnLu8Ik643haSD5WPQkAowOL5D6sS', 'patron', 'Silver Reel Member', 'https://api.dicebear.com/7.x/notionists/svg?seed=Rory'),
(10, 'River Song', 'river@patron.com', '$2y$12$eSUJY1n7u219LnGACRK3aer.EnLu8Ik643haSD5WPQkAowOL5D6sS', 'patron', 'Director Tier', 'https://api.dicebear.com/7.x/notionists/svg?seed=River'),
(11, 'Jack Harkness', 'jack@patron.com', '$2y$12$eSUJY1n7u219LnGACRK3aer.EnLu8Ik643haSD5WPQkAowOL5D6sS', 'patron', 'Silver Reel Member', 'https://api.dicebear.com/7.x/notionists/svg?seed=Jack');

-- 4. Insert Dummy Orders
INSERT INTO orders (user_id, movie_id, show_date, show_time, seats, num_seats, total_price, cc_number, cc_expiry, cc_cvc) VALUES
(2, 1, '2026-05-15', '14:00:00', 'C4, C5', 2, 24.00, '4111222233334444', '12/27', '123'),
(3, 1, '2026-05-15', '17:30:00', 'D6, D7', 2, 24.00, '5555666677778888', '11/26', '456'),
(4, 2, '2026-05-16', '14:00:00', 'B2', 1, 15.00, '4444555566667777', '09/25', '789'),
(5, 2, '2026-05-16', '17:30:00', 'E5, E6, E7', 3, 45.00, '4111222233334444', '08/28', '111'),
(6, 5, '2026-05-18', '21:30:00', 'A1, A2', 2, 30.00, '5555666677778888', '10/26', '222');
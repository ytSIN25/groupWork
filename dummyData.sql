-- ============================================
-- LUMIÈRE Cinema - Dummy Data Population Script
-- ============================================

-- Disable foreign key checks to allow clearing tables with dependencies
SET FOREIGN_KEY_CHECKS = 0;

-- Using DELETE instead of TRUNCATE to avoid #1701 constraints errors
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

-- Clean up users (except the main admin)
DELETE FROM users WHERE user_id > 1;
ALTER TABLE users AUTO_INCREMENT = 2;

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;

-- 1. Insert Sample Movies (Owned by Admin ID 1)
INSERT INTO movies (movie_id, movie_name, user_id, director, genre, release_year, starring, description, poster_path, duration, price, start_date) 
VALUES 
(1, 'Oppenheimer', 1, 'Christopher Nolan', 'Historical', 2023, 'Cillian Murphy', 'Atomic bomb biography.', 'assets/images/poster-oppenheimer.png', 180, 12.00, '2026-05-03'),
(2, 'Dune: Part Two', 1, 'Denis Villeneuve', 'Sci-Fi', 2024, 'Zendaya', 'Desert epic.', 'assets/images/poster-dune.png', 166, 15.00, '2026-05-08'),
(3, 'Nosferatu', 1, 'Robert Eggers', 'Horror', 2024, 'Bill Skarsgård', 'Gothic horror.', 'assets/images/poster-nosferatu.png', 125, 12.50, '2026-05-11'),
(4, 'Interstellar', 1, 'Christopher Nolan', 'Sci-Fi', 2014, 'Matthew McConaughey', 'Space travel.', 'assets/images/poster-interstellar.png', 169, 10.00, '2026-04-18'),
(5, 'Blade Runner 2049', 1, 'Denis Villeneuve', 'Sci-Fi', 2017, 'Ryan Gosling', 'Neon detective.', 'assets/images/poster-bladerunner.png', 164, 15.00, '2026-04-28');

-- 2. Insert Showtimes
INSERT INTO showtimes (showtime_id, movie_id, auditorium_number, show_date, start_time)
VALUES 
(1, 1, 1, '2026-05-08', '14:00:00'),
(2, 2, 2, '2026-05-08', '19:00:00'),
(3, 5, 3, '2026-05-08', '21:30:00');

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

-- 4. Insert 30 Orders
INSERT INTO orders (user_id, showtime_id, seats, num_seats, total_price, order_date) VALUES
(2, 1, 'A1,A2', 2, 24.00, '2026-05-04 10:00:00'),
(3, 1, 'B1', 1, 12.00, '2026-05-04 11:30:00'),
(4, 2, 'C3,C4,C5', 3, 45.00, '2026-05-04 14:20:00'),
(5, 2, 'D10', 1, 15.00, '2026-05-04 16:45:00'),
(6, 3, 'E1,E2', 2, 30.00, '2026-05-05 09:15:00'),
(7, 3, 'F5', 1, 15.00, '2026-05-05 10:30:00'),
(8, 1, 'G12', 1, 12.00, '2026-05-05 12:00:00'),
(9, 2, 'H1,H2,H3,H4', 4, 60.00, '2026-05-05 15:40:00'),
(10, 1, 'J1', 1, 12.00, '2026-05-05 18:20:00'),
(11, 3, 'K5,K6', 2, 30.00, '2026-05-06 08:00:00'),
(2, 2, 'L1', 1, 15.00, '2026-05-06 11:10:00'),
(3, 1, 'M3,M4', 2, 24.00, '2026-05-06 13:50:00'),
(4, 3, 'N10', 1, 15.00, '2026-05-06 17:00:00'),
(5, 1, 'P1,P2,P3', 3, 36.00, '2026-05-07 10:15:00'),
(6, 2, 'Q5', 1, 15.00, '2026-05-07 12:45:00'),
(7, 3, 'R12', 1, 15.00, '2026-05-07 14:30:00'),
(8, 1, 'S1,S2', 2, 24.00, '2026-05-07 16:55:00'),
(9, 2, 'T1', 1, 15.00, '2026-05-07 19:10:00'),
(10, 1, 'A3,A4', 2, 24.00, '2026-05-08 09:00:00'),
(11, 3, 'B2', 1, 15.00, '2026-05-08 10:45:00'),
(2, 2, 'C6,C7,C8', 3, 45.00, '2026-05-08 11:30:00'),
(3, 1, 'D11', 1, 12.00, '2026-05-08 12:15:00'),
(4, 3, 'E3,E4', 2, 30.00, '2026-05-08 13:40:00'),
(5, 2, 'F6', 1, 15.00, '2026-05-08 14:20:00'),
(6, 1, 'G13', 1, 12.00, '2026-05-08 15:05:00'),
(7, 3, 'H5,H6,H7,H8', 4, 60.00, '2026-05-08 16:00:00'),
(8, 2, 'J2', 1, 15.00, '2026-05-08 17:15:00'),
(9, 2, 'K1,K2', 2, 30.00, '2026-05-08 18:30:00'),
(10, 1, 'L2', 1, 12.00, '2026-05-08 19:45:00'),
(11, 3, 'M5,M6', 2, 30.00, '2026-05-08 20:30:00');
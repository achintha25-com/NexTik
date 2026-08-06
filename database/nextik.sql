CREATE DATABASE IF NOT EXISTS event_booking_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE event_booking_db;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS bookings;
DROP TABLE IF EXISTS ticket_options;
DROP TABLE IF EXISTS events;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS users;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    phone VARCHAR(20) NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('customer','organizer','admin') NOT NULL DEFAULT 'customer',
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE categories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    slug VARCHAR(120) NOT NULL UNIQUE,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE events (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    organizer_id BIGINT UNSIGNED NOT NULL,
    category_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT NOT NULL,
    venue VARCHAR(255) NOT NULL,
    city VARCHAR(100) NOT NULL DEFAULT 'Colombo',
    event_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NULL,
    price DECIMAL(10,2) NOT NULL DEFAULT 0,
    total_tickets INT UNSIGNED NOT NULL,
    available_tickets INT UNSIGNED NOT NULL,
    image VARCHAR(255) NULL,
    is_featured TINYINT(1) NOT NULL DEFAULT 0,
    status ENUM('draft','published','cancelled','postponed') NOT NULL DEFAULT 'published',
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_events_organizer FOREIGN KEY (organizer_id) REFERENCES users(id) ON DELETE RESTRICT,
    CONSTRAINT fk_events_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT,
    INDEX idx_events_public (status, event_date),
    INDEX idx_events_city (city)
) ENGINE=InnoDB;

CREATE TABLE ticket_options (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    event_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL DEFAULT 0,
    total_tickets INT UNSIGNED NOT NULL,
    available_tickets INT UNSIGNED NOT NULL,
    sort_order INT UNSIGNED NOT NULL DEFAULT 0,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_ticket_options_event FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    INDEX idx_ticket_options_event (event_id)
) ENGINE=InnoDB;

CREATE TABLE bookings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    event_id BIGINT UNSIGNED NOT NULL,
    ticket_option_id BIGINT UNSIGNED NULL,
    ticket_option_name VARCHAR(100) NULL,
    booking_reference VARCHAR(32) NOT NULL UNIQUE,
    quantity INT UNSIGNED NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    payment_method VARCHAR(30) NOT NULL DEFAULT 'card',
    payment_reference VARCHAR(32) NULL UNIQUE,
    payment_status ENUM('paid','failed') NOT NULL DEFAULT 'paid',
    paid_at TIMESTAMP NULL,
    status ENUM('confirmed','cancelled') NOT NULL DEFAULT 'confirmed',
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_bookings_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_bookings_event FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE RESTRICT,
    CONSTRAINT fk_bookings_ticket_option FOREIGN KEY (ticket_option_id) REFERENCES ticket_options(id) ON DELETE RESTRICT,
    INDEX idx_bookings_user (user_id),
    INDEX idx_bookings_event (event_id)
) ENGINE=InnoDB;

CREATE TABLE contact_messages (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    topic VARCHAR(100) NOT NULL DEFAULT 'General enquiry',
    message TEXT NOT NULL,
    status ENUM('open','replied','closed') NOT NULL DEFAULT 'open',
    reply_text TEXT NULL,
    replied_by BIGINT UNSIGNED NULL,
    replied_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_contact_messages_admin FOREIGN KEY (replied_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_contact_messages_status (status),
    INDEX idx_contact_messages_created (created_at)
) ENGINE=InnoDB;

-- Demo password for all accounts: password
INSERT INTO users (id, name, email, phone, password, role) VALUES
(1, 'System Admin', 'admin@nextik.lk', '0771234567', '$2y$10$Iq/irBATt1eABeyNSk56beZvQhpZQNc2hGSGSmj/tL2D76qrCm0qS', 'admin'),
(2, 'Colombo Events Co.', 'organizer@nextik.lk', '0772345678', '$2y$10$Iq/irBATt1eABeyNSk56beZvQhpZQNc2hGSGSmj/tL2D76qrCm0qS', 'organizer'),
(3, 'Demo Customer', 'customer@nextik.lk', '0773456789', '$2y$10$Iq/irBATt1eABeyNSk56beZvQhpZQNc2hGSGSmj/tL2D76qrCm0qS', 'customer');

INSERT INTO categories (id, name, slug) VALUES
(1, 'Trending Now', 'trending-now'),
(2, 'Concerts', 'concerts'),
(3, 'EDM', 'edm'),
(4, 'Sport', 'sport'),
(5, 'Family', 'family'),
(6, 'Tamil DJ', 'tamil-dj');

INSERT INTO events (id, organizer_id, category_id, title, slug, description, venue, city, event_date, start_time, end_time, price, total_tickets, available_tickets, is_featured, status) VALUES
(1, 2, 2, 'SUDBEAT SHOWCASE 2026', 'sudbeat-showcase-2026', 'Experience an unforgettable night of live performances, great vibes, and premium entertainment in Sri Lanka.', 'Port City Colombo', 'Colombo', DATE_ADD(CURDATE(), INTERVAL 31 DAY), '17:00:00', '23:59:00', 7000, 500, 500, 1, 'published'),
(2, 2, 3, 'ANJUNADEEP OPEN AIR DEBUT', 'anjunadeep-open-air-debut', 'Dance under the stars with an exceptional open-air EDM experience.', 'Colombo Lotus Tower', 'Colombo', DATE_ADD(CURDATE(), INTERVAL 45 DAY), '11:00:00', '23:59:00', 6000, 500, 500, 1, 'published'),
(3, 2, 4, 'MAIN CHAMPIONSHIP', 'main-championship', 'A day of high-energy competition and unforgettable sporting moments.', 'Sirimavo Bandaranaike Memorial Exhibition Centre', 'Colombo', DATE_ADD(CURDATE(), INTERVAL 20 DAY), '15:00:00', '22:00:00', 4000, 500, 500, 1, 'published'),
(4, 2, 5, 'I LOVE BRUNCH', 'i-love-brunch', 'A relaxed family-friendly day of food, music, and good company.', 'King and Queen Cantina', 'Colombo', DATE_ADD(CURDATE(), INTERVAL 60 DAY), '10:00:00', '15:00:00', 4000, 300, 300, 0, 'published'),
(5, 2, 6, 'BLOODY KATCHERI - TAMIL DJ NIGHT', 'bloody-katcheri-tamil-dj-night', 'A vibrant Tamil DJ night with live entertainment and dancing.', 'The Joint Pub and Grill', 'Colombo', DATE_ADD(CURDATE(), INTERVAL 25 DAY), '18:00:00', '23:59:00', 3000, 500, 500, 0, 'published'),
(6, 2, 1, 'GOLDEN HORIZON 2.0', 'golden-horizon-2', 'A premium coastal experience with music, food, and entertainment.', 'Ceylonica Beach Hotel', 'Kalutara', DATE_ADD(CURDATE(), INTERVAL 35 DAY), '10:00:00', '22:00:00', 4000, 500, 500, 0, 'published'),
(7, 2, 3, 'THE LOST CITY', 'the-lost-city', 'A next-generation EDM experience for music lovers.', 'Port City Colombo', 'Colombo', DATE_ADD(CURDATE(), INTERVAL 90 DAY), '18:00:00', '23:59:00', 5000, 500, 500, 1, 'published'),
(8, 2, 2, 'NIMNADA', 'nimnada', 'An intimate concert evening with outstanding local artists.', 'Dharmaraja College Auditorium', 'Kandy', DATE_ADD(CURDATE(), INTERVAL 28 DAY), '19:00:00', '22:00:00', 2500, 500, 500, 0, 'published');

INSERT INTO ticket_options (event_id, name, price, total_tickets, available_tickets, sort_order) VALUES
(1, 'VIP', 12000, 100, 98, 0),
(1, 'Standing', 7000, 400, 400, 1),
(2, 'General Admission', 6000, 500, 500, 0),
(3, 'General Admission', 4000, 500, 500, 0),
(4, 'General Admission', 4000, 300, 300, 0),
(5, 'General Admission', 3000, 500, 500, 0),
(6, 'General Admission', 4000, 500, 500, 0),
(7, 'General Admission', 5000, 500, 500, 0),
(8, 'General Admission', 2500, 500, 500, 0);

INSERT INTO bookings (user_id, event_id, ticket_option_id, ticket_option_name, booking_reference, quantity, unit_price, total_amount, payment_method, payment_reference, payment_status, paid_at, status) VALUES
(3, 1, 1, 'VIP', 'NT-DEMO2026', 2, 12000, 24000, 'card', 'NT-PAY-DEMO01', 'paid', NOW(), 'confirmed');

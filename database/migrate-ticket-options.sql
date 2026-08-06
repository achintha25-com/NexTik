-- Run this once on an existing NexTik database.
USE event_booking_db;

CREATE TABLE IF NOT EXISTS ticket_options (
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

ALTER TABLE bookings
    ADD COLUMN IF NOT EXISTS ticket_option_id BIGINT UNSIGNED NULL AFTER event_id,
    ADD COLUMN IF NOT EXISTS ticket_option_name VARCHAR(100) NULL AFTER ticket_option_id;

-- MySQL 8 may not support IF NOT EXISTS on ADD COLUMN; ignore duplicate column errors if re-running.

INSERT INTO ticket_options (event_id, name, price, total_tickets, available_tickets, sort_order)
SELECT e.id, 'General Admission', e.price, e.total_tickets, e.available_tickets, 0
FROM events e
WHERE NOT EXISTS (
    SELECT 1 FROM ticket_options t WHERE t.event_id = e.id
);

UPDATE bookings b
JOIN events e ON e.id = b.event_id
LEFT JOIN ticket_options t ON t.event_id = b.event_id AND t.name = 'General Admission'
SET b.ticket_option_id = t.id,
    b.ticket_option_name = COALESCE(b.ticket_option_name, 'General Admission')
WHERE b.ticket_option_id IS NULL AND t.id IS NOT NULL;

-- Optional demo upgrade for event 1
UPDATE ticket_options SET name = 'VIP', price = 12000, total_tickets = 100, available_tickets = 98, sort_order = 0
WHERE event_id = 1 AND name = 'General Admission'
LIMIT 1;

INSERT INTO ticket_options (event_id, name, price, total_tickets, available_tickets, sort_order)
SELECT 1, 'Standing', 7000, 400, 400, 1
WHERE EXISTS (SELECT 1 FROM events WHERE id = 1)
  AND NOT EXISTS (SELECT 1 FROM ticket_options WHERE event_id = 1 AND name = 'Standing');

UPDATE events e
JOIN (
    SELECT event_id,
           MIN(price) AS min_price,
           SUM(total_tickets) AS total_tickets,
           SUM(available_tickets) AS available_tickets
    FROM ticket_options
    GROUP BY event_id
) t ON t.event_id = e.id
SET e.price = t.min_price,
    e.total_tickets = t.total_tickets,
    e.available_tickets = t.available_tickets;

-- Run once on an existing NexTik database.
USE event_booking_db;

ALTER TABLE bookings
    ADD COLUMN payment_method VARCHAR(30) NOT NULL DEFAULT 'card' AFTER total_amount,
    ADD COLUMN payment_reference VARCHAR(32) NULL AFTER payment_method,
    ADD COLUMN payment_status ENUM('paid','failed') NOT NULL DEFAULT 'paid' AFTER payment_reference,
    ADD COLUMN paid_at TIMESTAMP NULL AFTER payment_status;

UPDATE bookings
SET payment_reference = CONCAT('NT-PAY-', UPPER(SUBSTRING(MD5(CONCAT(id, booking_reference)), 1, 8))),
    payment_status = 'paid',
    paid_at = COALESCE(paid_at, created_at)
WHERE payment_reference IS NULL;

ALTER TABLE bookings ADD UNIQUE INDEX idx_bookings_payment_reference (payment_reference);

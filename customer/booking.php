<?php

require dirname(__DIR__).'/includes/bootstrap.php';

$user = require_role('customer');
$bookingId = (int) ($_GET['id'] ?? 0);

if ($bookingId && query_string('page') !== 'book' && ! isset($_GET['event_id'])) {
    $booking = booking_by_id($bookingId);
    if (! $booking || (int) $booking['user_id'] !== (int) $user['id']) {
        http_response_code(404);
        render('error', ['title' => 'Booking not found', 'message' => 'The requested booking could not be found.']);
        exit;
    }
    render('bookings/show', ['title' => 'Booking '.$booking['booking_reference'], 'booking' => $booking]);
    exit;
}

$event = event_by_id((int) ($_GET['event_id'] ?? $_GET['id'] ?? 0), true);
if (! $event) {
    http_response_code(404);
    render('error', ['title' => 'Event not found', 'message' => 'The requested event is unavailable.']);
    exit;
}
$errors = [];
if ($event['event_date'] < date('Y-m-d')) $errors[] = 'This event has already taken place.';
if ((int) $event['available_tickets'] < 1) $errors[] = 'This event is sold out.';
if (is_post()) {
    verify_csrf();
    $quantity = filter_var($_POST['quantity'] ?? null, FILTER_VALIDATE_INT);
    if (! $quantity || $quantity < 1 || $quantity > 10) $errors[] = 'Choose between 1 and 10 tickets.';
    if ($errors === []) {
        $pdo = db();
        try {
            $pdo->beginTransaction();
            $statement = $pdo->prepare('SELECT id, price, available_tickets, event_date, status FROM events WHERE id = ? FOR UPDATE');
            $statement->execute([$event['id']]);
            $locked = $statement->fetch();
            if (! $locked || $locked['status'] !== 'published' || $locked['event_date'] < date('Y-m-d')) throw new RuntimeException('This event is no longer available.');
            if ((int) $locked['available_tickets'] < $quantity) throw new RuntimeException('Not enough tickets are available.');
            do {
                $reference = 'NT-'.strtoupper(bin2hex(random_bytes(4)));
                $check = $pdo->prepare('SELECT COUNT(*) FROM bookings WHERE booking_reference = ?');
                $check->execute([$reference]);
            } while ($check->fetchColumn());
            $statement = $pdo->prepare("INSERT INTO bookings (user_id, event_id, booking_reference, quantity, unit_price, total_amount, status) VALUES (?, ?, ?, ?, ?, ?, 'confirmed')");
            $statement->execute([$user['id'], $event['id'], $reference, $quantity, $locked['price'], $locked['price'] * $quantity]);
            $newBooking = (int) $pdo->lastInsertId();
            $pdo->prepare('UPDATE events SET available_tickets = available_tickets - ? WHERE id = ?')->execute([$quantity, $event['id']]);
            $pdo->commit();
            flash('success', 'Your booking was confirmed.');
            redirect_to('booking', ['id' => $newBooking]);
        } catch (Throwable $exception) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            $errors[] = $exception instanceof RuntimeException ? $exception->getMessage() : 'The booking could not be completed.';
        }
    }
}
render('bookings/form', ['title' => 'Book '.$event['title'], 'event' => $event, 'errors' => $errors]);

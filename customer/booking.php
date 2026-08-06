<?php

require dirname(__DIR__).'/includes/bootstrap.php';

$user = require_role('customer');
$bookingId = (int) ($_GET['id'] ?? 0);
$step = query_string('step');

if ($bookingId && $step !== 'pay' && query_string('page') !== 'book' && ! isset($_GET['event_id'])) {
    $booking = booking_by_id($bookingId);
    if (! $booking || (int) $booking['user_id'] !== (int) $user['id']) {
        http_response_code(404);
        render('error', ['title' => 'Booking not found', 'message' => 'The requested booking could not be found.']);
        exit;
    }
    render('bookings/show', ['title' => 'Booking '.$booking['booking_reference'], 'booking' => $booking]);
    exit;
}

$eventId = (int) ($_GET['event_id'] ?? $_GET['id'] ?? 0);
$event = event_by_id($eventId, true);
if (! $event) {
    http_response_code(404);
    render('error', ['title' => 'Event not found', 'message' => 'The requested event is unavailable.']);
    exit;
}

$ticketOptions = ticket_options_for_event((int) $event['id']);
$availableOptions = array_values(array_filter(
    $ticketOptions,
    static fn (array $option): bool => (int) $option['available_tickets'] > 0
));

$errors = [];
if ($event['event_date'] < date('Y-m-d')) $errors[] = 'This event has already taken place.';
if ($availableOptions === []) $errors[] = 'This event is sold out.';

if ($step === 'pay') {
    $pending = pending_booking();
    if (! $pending || (int) $pending['event_id'] !== (int) $event['id']) {
        flash('error', 'Your checkout session expired. Choose your tickets again.');
        redirect_to('book', ['id' => $event['id']]);
    }

    $selectedOption = null;
    foreach ($ticketOptions as $option) {
        if ((int) $option['id'] === (int) $pending['ticket_option_id']) {
            $selectedOption = $option;
            break;
        }
    }

    if (! $selectedOption || (int) $selectedOption['available_tickets'] < (int) $pending['quantity']) {
        clear_pending_booking();
        flash('error', 'Those tickets are no longer available. Choose another option.');
        redirect_to('book', ['id' => $event['id']]);
    }

    $pendingTotal = (float) $selectedOption['price'] * (int) $pending['quantity'];

    if (is_post()) {
        verify_csrf();
        [$paymentErrors] = payment_validation($_POST);
        $errors = array_merge($errors, $paymentErrors);

        if ($errors === []) {
            try {
                $paymentReference = payment_reference();
                $newBooking = complete_booking(
                    (int) $user['id'],
                    (int) $event['id'],
                    (int) $pending['ticket_option_id'],
                    (int) $pending['quantity'],
                    $paymentReference
                );
                clear_pending_booking();
                flash('success', 'Payment successful. Your booking is confirmed.');
                redirect_to('booking', ['id' => $newBooking]);
            } catch (Throwable $exception) {
                $errors[] = $exception instanceof RuntimeException ? $exception->getMessage() : 'Payment could not be completed.';
            }
        }
    }

    render('bookings/payment', [
        'title' => 'Pay for '.$event['title'],
        'event' => $event,
        'pending' => $pending,
        'selectedOption' => $selectedOption,
        'pendingTotal' => $pendingTotal,
        'errors' => $errors,
    ]);
    exit;
}

if (is_post()) {
    verify_csrf();
    $ticketOptionId = filter_var($_POST['ticket_option_id'] ?? null, FILTER_VALIDATE_INT);
    $quantity = filter_var($_POST['quantity'] ?? null, FILTER_VALIDATE_INT);

    if (! $ticketOptionId) $errors[] = 'Choose a ticket option.';
    if (! $quantity || $quantity < 1 || $quantity > 10) $errors[] = 'Choose between 1 and 10 tickets.';

    if ($errors === []) {
        $statement = db()->prepare('SELECT id, available_tickets FROM ticket_options WHERE id = ? AND event_id = ?');
        $statement->execute([$ticketOptionId, $event['id']]);
        $option = $statement->fetch();
        if (! $option) {
            $errors[] = 'Choose a valid ticket option.';
        } elseif ((int) $option['available_tickets'] < $quantity) {
            $errors[] = 'Not enough tickets are available for that option.';
        } else {
            set_pending_booking([
                'event_id' => (int) $event['id'],
                'ticket_option_id' => $ticketOptionId,
                'quantity' => $quantity,
            ]);
            redirect_to('book', ['id' => $event['id'], 'step' => 'pay']);
        }
    }
}

render('bookings/form', [
    'title' => 'Book '.$event['title'],
    'event' => $event,
    'ticketOptions' => $ticketOptions,
    'availableOptions' => $availableOptions,
    'errors' => $errors,
]);

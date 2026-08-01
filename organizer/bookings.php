<?php

declare(strict_types=1);

require dirname(__DIR__).'/includes/bootstrap.php';

$user = require_role('organizer');
$event = event_by_id((int) ($_GET['id'] ?? 0));

if (! $event || (int) $event['organizer_id'] !== (int) $user['id']) {
    http_response_code(404);
    render('error', ['title' => 'Event not found', 'message' => 'The requested event could not be found.']);
    exit;
}

$statement = db()->prepare(
    'SELECT b.*, u.name AS customer_name, u.email AS customer_email
     FROM bookings b
     JOIN users u ON u.id = b.user_id
     WHERE b.event_id = ?
     ORDER BY b.created_at DESC'
);
$statement->execute([$event['id']]);
$bookings = $statement->fetchAll();

$title = 'Event bookings';
$flashMessages = consume_flash();

require dirname(__DIR__).'/includes/header.php';
?>

<section class="page-header">
    <div class="container">
        <p class="eyebrow">ORGANIZER WORKSPACE</p>
        <h1>Bookings for <?= e($event['title']) ?></h1>
        <p><?= number_format((int) $event['available_tickets']) ?> tickets remaining.</p>
        <div class="page-toolbar"><a class="btn btn-secondary" href="<?= e(app_url('organizer-events')) ?>">Back to events</a></div>
    </div>
</section>

<section class="section compact-top">
    <div class="container">
        <div class="table-card">
            <table>
                <thead>
                    <tr><th>Reference</th><th>Customer</th><th>Email</th><th>Tickets</th><th>Total</th><th>Status</th></tr>
                </thead>
                <tbody>
                    <?php if (! $bookings): ?>
                        <tr><td colspan="6" class="empty-state">No bookings for this event yet.</td></tr>
                    <?php endif; ?>

                    <?php foreach ($bookings as $booking): ?>
                        <tr>
                            <td><?= e($booking['booking_reference']) ?></td>
                            <td><?= e($booking['customer_name']) ?></td>
                            <td><?= e($booking['customer_email']) ?></td>
                            <td><?= (int) $booking['quantity'] ?></td>
                            <td>LKR <?= number_format((float) $booking['total_amount'], 2) ?></td>
                            <td><span class="status-badge status-<?= e($booking['status']) ?>"><?= e(ucfirst($booking['status'])) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<?php require dirname(__DIR__).'/includes/footer.php'; ?>

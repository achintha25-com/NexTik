<?php

declare(strict_types=1);

require dirname(__DIR__).'/includes/bootstrap.php';

$user = require_role('organizer');

$eventStatement = db()->prepare(
    'SELECT e.*, c.name AS category_name
     FROM events e
     JOIN categories c ON c.id = e.category_id
     WHERE e.organizer_id = ?
     ORDER BY e.event_date, e.start_time'
);
$eventStatement->execute([$user['id']]);
$events = $eventStatement->fetchAll();

$bookingStatsStatement = db()->prepare(
    "SELECT
        COUNT(*) AS booking_count,
        COALESCE(SUM(quantity), 0) AS tickets_sold,
        COALESCE(SUM(total_amount), 0) AS revenue
     FROM bookings b
     JOIN events e ON e.id = b.event_id
     WHERE e.organizer_id = ? AND b.status = 'confirmed' AND b.payment_status = 'paid'"
);
$bookingStatsStatement->execute([$user['id']]);
$bookingStats = $bookingStatsStatement->fetch() ?: [
    'booking_count' => 0,
    'tickets_sold' => 0,
    'revenue' => 0,
];

$recentBookingStatement = db()->prepare(
    'SELECT b.booking_reference, b.quantity, b.total_amount, b.status, b.created_at,
            e.title AS event_title, u.name AS customer_name
     FROM bookings b
     JOIN events e ON e.id = b.event_id
     JOIN users u ON u.id = b.user_id
     WHERE e.organizer_id = ?
     ORDER BY b.created_at DESC
     LIMIT 5'
);
$recentBookingStatement->execute([$user['id']]);
$recentBookings = $recentBookingStatement->fetchAll();

$today = date('Y-m-d');
$upcomingEvents = array_values(array_filter(
    $events,
    static fn (array $event): bool => $event['event_date'] >= $today && $event['status'] !== 'cancelled'
));

$publishedEvents = count(array_filter(
    $events,
    static fn (array $event): bool => $event['status'] === 'published'
));

$title = 'Organizer dashboard';
$bodyClass = 'organizer-page organizer-workspace';
$flashMessages = consume_flash();

require dirname(__DIR__).'/includes/header.php';
?>

<section class="plain-page-head">
    <div class="container plain-page-head-inner">
        <div>
            <span class="plain-page-label">Organizer workspace</span>
            <h1>Dashboard overview</h1>
            <p>Welcome back, <?= e($user['name']) ?>. Manage your events and bookings.</p>
        </div>
    </div>
</section>

<section class="section organizer-dashboard-section">
    <div class="container">
        <div class="organizer-welcome">
            <div>
                <p class="eyebrow">YOUR OVERVIEW</p>
                <h2>Welcome back, <?= e($user['name']) ?>.</h2>
                <p>Keep your events moving and stay close to every reservation.</p>
            </div>
            <div class="organizer-dashboard-toolbar">
                <a class="btn btn-primary" href="<?= e(app_url('organizer-event-form')) ?>">Create event</a>
                <a class="btn btn-secondary" href="<?= e(app_url('organizer-events')) ?>">Manage events</a>
            </div>
        </div>

        <div class="organizer-metric-grid">
            <div class="organizer-metric-card">
                <span class="organizer-metric-icon">EV</span>
                <strong><?= count($events) ?></strong>
                <small>Total events</small>
                <em><?= $publishedEvents ?> published</em>
            </div>
            <div class="organizer-metric-card">
                <span class="organizer-metric-icon">BK</span>
                <strong><?= number_format((int) $bookingStats['booking_count']) ?></strong>
                <small>Confirmed bookings</small>
                <em>Across your events</em>
            </div>
            <div class="organizer-metric-card">
                <span class="organizer-metric-icon">TK</span>
                <strong><?= number_format((int) $bookingStats['tickets_sold']) ?></strong>
                <small>Tickets sold</small>
                <em>Confirmed reservations</em>
            </div>
            <div class="organizer-metric-card organizer-revenue-card">
                <span class="organizer-metric-icon">LKR</span>
                <strong><?= number_format((float) $bookingStats['revenue'], 0) ?></strong>
                <small>Confirmed revenue</small>
                <em>Before any adjustments</em>
            </div>
        </div>

        <div class="organizer-dashboard-grid">
            <section class="dashboard-panel upcoming-events-panel">
                <div class="dashboard-panel-heading">
                    <div>
                        <p class="eyebrow">UP NEXT</p>
                        <h2>Upcoming events</h2>
                    </div>
                    <a class="text-link" href="<?= e(app_url('organizer-events')) ?>">View all <span>&rarr;</span></a>
                </div>

                <?php if ($upcomingEvents === []): ?>
                    <div class="dashboard-empty-state">
                        <strong>Your next event starts here.</strong>
                        <p>Create an event to begin building your programme.</p>
                        <a class="btn btn-primary btn-sm" href="<?= e(app_url('organizer-event-form')) ?>">Create event</a>
                    </div>
                <?php else: ?>
                    <div class="organizer-event-list">
                        <?php foreach (array_slice($upcomingEvents, 0, 4) as $event): ?>
                            <?php
                            $ticketsSold = (int) $event['total_tickets'] - (int) $event['available_tickets'];
                            $soldPercentage = (int) $event['total_tickets'] > 0
                                ? min(100, (int) round(($ticketsSold / (int) $event['total_tickets']) * 100))
                                : 0;
                            ?>
                            <article class="organizer-event-row">
                                <div class="organizer-event-date">
                                    <strong><?= e(date('d', strtotime($event['event_date']))) ?></strong>
                                    <span><?= e(date('M', strtotime($event['event_date']))) ?></span>
                                </div>
                                <div class="organizer-event-details">
                                    <div class="organizer-event-title-row">
                                        <div>
                                            <h3><?= e($event['title']) ?></h3>
                                            <p><?= e($event['venue']) ?>, <?= e($event['city']) ?></p>
                                        </div>
                                        <span class="status-badge status-<?= e($event['status']) ?>"><?= e(ucfirst($event['status'])) ?></span>
                                    </div>
                                    <div class="organizer-progress-line">
                                        <span><b><?= number_format($ticketsSold) ?></b> / <?= number_format((int) $event['total_tickets']) ?> tickets sold</span>
                                        <span><?= $soldPercentage ?>%</span>
                                    </div>
                                    <div class="organizer-progress"><span style="width: <?= $soldPercentage ?>%"></span></div>
                                </div>
                                <a class="icon-btn organizer-row-action" href="<?= e(app_url('organizer-event-form', ['id' => $event['id']])) ?>" title="Edit event" aria-label="Edit <?= e($event['title']) ?>">
                                    <?= icon('edit') ?>
                                </a>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>

            <section class="dashboard-panel recent-bookings-panel">
                <div class="dashboard-panel-heading">
                    <div>
                        <p class="eyebrow">LATEST ACTIVITY</p>
                        <h2>Recent bookings</h2>
                    </div>
                </div>

                <?php if ($recentBookings === []): ?>
                    <div class="dashboard-empty-state compact-empty">
                        <strong>No bookings yet.</strong>
                        <p>Confirmed reservations will appear here.</p>
                    </div>
                <?php else: ?>
                    <div class="recent-booking-list">
                        <?php foreach ($recentBookings as $booking): ?>
                            <article class="recent-booking-row">
                                <div class="recent-booking-avatar"><?= e(mb_strtoupper(mb_substr($booking['customer_name'], 0, 1))) ?></div>
                                <div class="recent-booking-details">
                                    <h3><?= e($booking['customer_name']) ?></h3>
                                    <p><?= e($booking['event_title']) ?></p>
                                    <small><?= e($booking['booking_reference']) ?> &bull; <?= e(date('M j, g:i A', strtotime($booking['created_at']))) ?></small>
                                </div>
                                <div class="recent-booking-total">
                                    <strong>LKR <?= number_format((float) $booking['total_amount'], 0) ?></strong>
                                    <span class="status-badge status-<?= e($booking['status']) ?>"><?= e(ucfirst($booking['status'])) ?></span>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>
        </div>
    </div>
</section>

<?php require dirname(__DIR__).'/includes/footer.php'; ?>

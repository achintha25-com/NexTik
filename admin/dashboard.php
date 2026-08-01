<?php

declare(strict_types=1);

require dirname(__DIR__).'/includes/bootstrap.php';

require_role('admin');

$stats = [
    'users' => (int) db()->query('SELECT COUNT(*) FROM users')->fetchColumn(),
    'events' => (int) db()->query('SELECT COUNT(*) FROM events')->fetchColumn(),
    'bookings' => (int) db()->query('SELECT COUNT(*) FROM bookings')->fetchColumn(),
    'revenue' => (float) db()->query("SELECT COALESCE(SUM(total_amount), 0) FROM bookings WHERE status = 'confirmed'")->fetchColumn(),
];

$openMessages = (int) db()->query("SELECT COUNT(*) FROM contact_messages WHERE status = 'open'")->fetchColumn();

$recentBookings = db()->query(
    'SELECT b.*, u.name AS customer_name, e.title AS event_title
     FROM bookings b
     JOIN users u ON u.id = b.user_id
     JOIN events e ON e.id = b.event_id
     ORDER BY b.created_at DESC
     LIMIT 8'
)->fetchAll();

$title = 'Admin dashboard';
$bodyClass = 'admin-page';
$user = current_user();
$flashMessages = consume_flash();

require dirname(__DIR__).'/includes/header.php';
?>

<section class="about-hero admin-hero">
    <div class="about-hero-backdrop" aria-hidden="true"></div>
    <div class="container about-hero-layout">
        <div class="original-copy">
            <span class="original-kicker"><i></i> ADMINISTRATION</span>
            <h1>Keep NexTik <span>running smoothly.</span></h1>
        </div>
    </div>
</section>

<section class="section compact-top">
    <div class="container">
        <div class="page-toolbar admin-dashboard-toolbar">
            <a class="btn btn-primary" href="<?= e(app_url('admin-event-form')) ?>">Create event</a>
            <a class="btn btn-secondary" href="<?= e(app_url('admin-events')) ?>">Manage events</a>
            <a class="btn btn-secondary" href="<?= e(app_url('admin-categories')) ?>">Categories</a>
            <a class="btn btn-secondary" href="<?= e(app_url('admin-messages')) ?>">Messages<?= $openMessages ? ' ('.number_format($openMessages).')' : '' ?></a>
        </div>

        <div class="stats-grid">
            <div class="stat-card"><h3><?= number_format($stats['users']) ?></h3><p>Registered users</p></div>
            <div class="stat-card"><h3><?= number_format($stats['events']) ?></h3><p>Total events</p></div>
            <div class="stat-card"><h3><?= number_format($stats['bookings']) ?></h3><p>Total bookings</p></div>
            <div class="stat-card"><h3>LKR <?= number_format($stats['revenue'], 2) ?></h3><p>Confirmed revenue</p></div>
        </div>

        <div class="table-card">
            <h2>Recent bookings</h2>
            <table>
                <thead><tr><th>Reference</th><th>Customer</th><th>Event</th><th>Total</th><th>Status</th></tr></thead>
                <tbody>
                    <?php if (! $recentBookings): ?>
                        <tr><td colspan="5" class="empty-state">No bookings yet.</td></tr>
                    <?php endif; ?>

                    <?php foreach ($recentBookings as $booking): ?>
                        <tr>
                            <td><a class="text-link" href="<?= e(app_url('booking', ['id' => $booking['id']])) ?>"><?= e($booking['booking_reference']) ?></a></td>
                            <td><?= e($booking['customer_name']) ?></td>
                            <td><?= e($booking['event_title']) ?></td>
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

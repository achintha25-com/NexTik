<?php

declare(strict_types=1);

require dirname(__DIR__).'/includes/bootstrap.php';

require_role('admin');

$stats = [
    'customers' => (int) db()->query("SELECT COUNT(*) FROM users WHERE role = 'customer'")->fetchColumn(),
    'organizers' => (int) db()->query("SELECT COUNT(*) FROM users WHERE role = 'organizer'")->fetchColumn(),
    'admins' => (int) db()->query("SELECT COUNT(*) FROM users WHERE role = 'admin'")->fetchColumn(),
    'events' => (int) db()->query('SELECT COUNT(*) FROM events')->fetchColumn(),
    'bookings' => (int) db()->query('SELECT COUNT(*) FROM bookings')->fetchColumn(),
    'revenue' => (float) db()->query("SELECT COALESCE(SUM(total_amount), 0) FROM bookings WHERE status = 'confirmed' AND payment_status = 'paid'")->fetchColumn(),
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
$bodyClass = 'admin-page admin-workspace';
$user = current_user();
$flashMessages = consume_flash();

require dirname(__DIR__).'/includes/header.php';
?>

<div class="admin-dashboard">
    <section class="plain-page-head">
        <div class="container plain-page-head-inner">
            <div>
                <span class="plain-page-label">Administration</span>
                <h1>Dashboard overview</h1>
                <p>Welcome back, <?= e($user['name'] ?? 'Administrator') ?>. Here is the latest NexTik activity.</p>
            </div>
            <div class="plain-page-actions"><a class="btn btn-primary" href="<?= e(app_url('admin-event-form')) ?>">+ Create event</a></div>
        </div>
    </section>

    <section class="admin-dashboard-content">
        <div class="container">
            <nav class="admin-quick-actions" aria-label="Dashboard actions">
                <a href="<?= e(app_url('admin-users',['role'=>'customer'])) ?>"><strong>Customers</strong><span>Manage customer accounts</span></a>
                <a href="<?= e(app_url('admin-users',['role'=>'organizer'])) ?>"><strong>Organizers</strong><span>Manage event organizers</span></a>
                <a href="<?= e(app_url('admin-users',['role'=>'admin'])) ?>"><strong>Administrators</strong><span>Manage admin access</span></a>
                <a href="<?= e(app_url('admin-events')) ?>"><strong>Events</strong><span>Manage all events</span></a>
                <a href="<?= e(app_url('admin-messages')) ?>"><strong>Messages</strong><span><?= $openMessages ? number_format($openMessages).' awaiting reply' : 'No unread messages' ?></span></a>
                <a href="<?= e(app_url('admin-reports')) ?>"><strong>Reports</strong><span>Bookings and revenue</span></a>
            </nav>

            <div class="admin-metrics">
                <article class="admin-metric-card"><span class="admin-metric-label">Customers</span><strong><?= number_format($stats['customers']) ?></strong><small>Customer accounts</small></article>
                <article class="admin-metric-card"><span class="admin-metric-label">Organizers</span><strong><?= number_format($stats['organizers']) ?></strong><small>Event organizers</small></article>
                <article class="admin-metric-card"><span class="admin-metric-label">Administrators</span><strong><?= number_format($stats['admins']) ?></strong><small>Admin accounts</small></article>
                <article class="admin-metric-card"><span class="admin-metric-label">Total events</span><strong><?= number_format($stats['events']) ?></strong><small>Published listings</small></article>
                <article class="admin-metric-card"><span class="admin-metric-label">Total bookings</span><strong><?= number_format($stats['bookings']) ?></strong><small>All reservations</small></article>
                <article class="admin-metric-card admin-metric-revenue"><span class="admin-metric-label">Confirmed revenue</span><strong>LKR <?= number_format($stats['revenue'], 2) ?></strong><small>Paid bookings</small></article>
            </div>

            <section class="admin-bookings-panel">
                <div class="admin-panel-heading">
                    <div><span class="admin-eyebrow">Latest activity</span><h2>Recent bookings</h2></div>
                    <span><?= number_format(count($recentBookings)) ?> recent records</span>
                </div>
                <div class="admin-table-wrap">
            <table>
                <thead><tr><th>Reference</th><th>Customer</th><th>Event</th><th>Total</th><th>Status</th><th>Action</th></tr></thead>
                <tbody>
                    <?php if (! $recentBookings): ?>
                        <tr><td colspan="6" class="empty-state">No bookings yet.</td></tr>
                    <?php endif; ?>

                    <?php foreach ($recentBookings as $booking): ?>
                        <tr>
                            <td><strong><?= e($booking['booking_reference']) ?></strong></td>
                            <td><?= e($booking['customer_name']) ?></td>
                            <td><?= e($booking['event_title']) ?></td>
                            <td>LKR <?= number_format((float) $booking['total_amount'], 2) ?></td>
                            <td><span class="status-badge status-<?= e($booking['status']) ?>"><?= e(ucfirst($booking['status'])) ?></span></td>
                            <td>
                                <div class="table-actions">
                                    <a class="icon-btn" href="<?= e(app_url('booking', ['id' => $booking['id']])) ?>" title="View details" aria-label="View booking <?= e($booking['booking_reference']) ?>">
                                        <?= icon('view') ?>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
                </div>
            </section>
        </div>
    </section>
</div>

<?php require dirname(__DIR__).'/includes/footer.php'; ?>

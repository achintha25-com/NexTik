<?php

declare(strict_types=1);

require dirname(__DIR__).'/includes/bootstrap.php';

require_role('admin');

if ((query_string('action') === 'delete' || query_string('page') === 'admin-event-delete') && is_post()) {
    verify_csrf();

    $eventId = (int) ($_GET['id'] ?? 0);
    $bookingCheck = db()->prepare('SELECT COUNT(*) FROM bookings WHERE event_id = ?');
    $bookingCheck->execute([$eventId]);

    if ($bookingCheck->fetchColumn()) {
        flash('error', 'Events with booking history cannot be deleted. Mark the event as cancelled instead.');
    } else {
        db()->prepare('DELETE FROM events WHERE id = ?')->execute([$eventId]);
        flash('success', 'Event deleted successfully.');
    }

    redirect_to('admin-events');
}

$events = db()->query(
    'SELECT e.*, c.name AS category_name, u.name AS organizer_name
     FROM events e
     JOIN categories c ON c.id = e.category_id
     JOIN users u ON u.id = e.organizer_id
     ORDER BY e.created_at DESC'
)->fetchAll();

$title = 'Manage events';
$user = current_user();
$flashMessages = consume_flash();

require dirname(__DIR__).'/includes/header.php';
?>

<section class="page-header">
    <div class="container">
        <p class="eyebrow">ADMINISTRATION</p>
        <h1>Manage events</h1>
        <p>Review and manage every published or draft event.</p>
        <div class="page-toolbar">
            <a class="btn btn-primary" href="<?= e(app_url('admin-event-form')) ?>">Create event</a>
            <a class="btn btn-secondary" href="<?= e(app_url('admin-dashboard')) ?>">Dashboard</a>
            <a class="btn btn-secondary" href="<?= e(app_url('admin-categories')) ?>">Categories</a>
        </div>
    </div>
</section>

<section class="section compact-top">
    <div class="container">
        <div class="table-card">
            <table>
                <thead>
                    <tr>
                        <th>Event</th>
                        <th>Date</th>
                        <th>Organizer</th>
                        <th>Tickets</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (! $events): ?>
                        <tr><td colspan="6" class="empty-state">No events created yet.</td></tr>
                    <?php endif; ?>

                    <?php foreach ($events as $event): ?>
                        <tr>
                            <td>
                                <strong><?= e($event['title']) ?></strong>
                                <small class="table-subtitle"><?= e($event['category_name']) ?> - <?= e($event['city']) ?></small>
                            </td>
                            <td><?= e(date('M j, Y', strtotime($event['event_date']))) ?></td>
                            <td><?= e($event['organizer_name']) ?></td>
                            <td><?= number_format((int) $event['available_tickets']) ?> / <?= number_format((int) $event['total_tickets']) ?></td>
                            <td><span class="status-badge status-<?= e($event['status']) ?>"><?= e(ucfirst($event['status'])) ?></span></td>
                            <td>
                                <div class="table-actions">
                                    <a class="icon-btn" href="<?= e(app_url('admin-event-form', ['id' => $event['id']])) ?>" title="Edit event" aria-label="Edit <?= e($event['title']) ?>">
                                        <?= icon('edit') ?>
                                    </a>
                                    <form method="post" action="<?= e(app_url('admin-event-delete', ['id' => $event['id']])) ?>" data-confirm="Delete this event?">
                                        <?= csrf_field() ?>
                                        <button class="icon-btn icon-btn-danger" type="submit" title="Delete event" aria-label="Delete <?= e($event['title']) ?>">
                                            <?= icon('delete') ?>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<?php require dirname(__DIR__).'/includes/footer.php'; ?>

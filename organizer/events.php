<?php

declare(strict_types=1);

require dirname(__DIR__).'/includes/bootstrap.php';

$user = require_role('organizer');

$statement = db()->prepare(
    'SELECT e.*, c.name AS category_name
     FROM events e
     JOIN categories c ON c.id = e.category_id
     WHERE e.organizer_id = ?
     ORDER BY e.created_at DESC'
);
$statement->execute([$user['id']]);
$events = $statement->fetchAll();

$title = 'My events';
$bodyClass = 'organizer-page organizer-workspace';
$flashMessages = consume_flash();

require dirname(__DIR__).'/includes/header.php';
?>

<section class="plain-page-head">
    <div class="container plain-page-head-inner">
        <div><span class="plain-page-label">Organizer workspace</span><h1>My events</h1><p>Create events and track their booking performance.</p></div>
        <div class="plain-page-actions">
            <a class="btn btn-primary" href="<?= e(app_url('organizer-event-form')) ?>">Create event</a>
        </div>
    </div>
</section>

<section class="section compact-top">
    <div class="container">
        <div class="table-card">
            <table>
                <thead>
                    <tr><th>Event</th><th>Date</th><th>Tickets</th><th>Status</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    <?php if (! $events): ?>
                        <tr><td colspan="5" class="empty-state">No events created yet.</td></tr>
                    <?php endif; ?>

                    <?php foreach ($events as $event): ?>
                        <tr>
                            <td>
                                <strong><?= e($event['title']) ?></strong>
                                <small class="table-subtitle"><?= e($event['category_name']) ?> - <?= e($event['city']) ?></small>
                            </td>
                            <td><?= e(date('M j, Y', strtotime($event['event_date']))) ?></td>
                            <td><?= number_format((int) $event['available_tickets']) ?> / <?= number_format((int) $event['total_tickets']) ?></td>
                            <td><span class="status-badge status-<?= e($event['status']) ?>"><?= e(ucfirst($event['status'])) ?></span></td>
                            <td>
                                <div class="table-actions">
                                    <a class="icon-btn" href="<?= e(app_url('organizer-event-form', ['id' => $event['id']])) ?>" title="Edit event" aria-label="Edit <?= e($event['title']) ?>">
                                        <?= icon('edit') ?>
                                    </a>
                                    <a class="icon-btn" href="<?= e(app_url('organizer-event-bookings', ['id' => $event['id']])) ?>" title="View bookings" aria-label="View bookings for <?= e($event['title']) ?>">
                                        <?= icon('ticket') ?>
                                    </a>
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

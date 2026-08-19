<?php

require __DIR__.'/includes/bootstrap.php';

$event = event_by_id((int) ($_GET['id'] ?? 0), true);
if (! $event) {
    http_response_code(404);
    render('error', ['title' => 'Event not found', 'message' => 'The requested event is not available.']);
    exit;
}

$ticketOptions = ticket_options_for_event((int) $event['id']);
$hasAvailability = (int) $event['available_tickets'] > 0;

$title = $event['title'];
$user = current_user();
$flashMessages = consume_flash();
$eventImage = match ($event['category_slug']) {
    'concerts' => 'concert-card.png',
    'edm' => 'edm-card.png',
    default => 'hero-nextik.png',
};
$eventImagePath = ! empty($event['image']) ? 'images/'.ltrim((string) $event['image'], '/') : 'images/'.$eventImage;
require __DIR__.'/includes/header.php';
?>
<section class="event-detail-page">
<div class="container event-detail-container">
    <a class="event-back-link" href="<?= e(app_url()) ?>"><span aria-hidden="true">←</span> All events</a>

    <header class="event-detail-heading">
        <div>
            <span class="event-category-label"><?= e(strtoupper($event['category_name'])) ?></span>
            <h1><?= e($event['title']) ?></h1>
            <p><?= e($event['venue']) ?> <span>•</span> <?= e($event['city']) ?></p>
        </div>
        <div class="event-heading-meta" aria-label="Event schedule">
            <div><small>Date</small><strong><?= e(date('M j, Y', strtotime($event['event_date']))) ?></strong></div>
            <div><small>Starts</small><strong><?= e(date('g:i A', strtotime($event['start_time']))) ?></strong></div>
        </div>
    </header>

    <div class="event-experience-grid">
        <figure class="event-poster-panel">
            <img src="<?= e(asset_url($eventImagePath)) ?>" alt="<?= e($event['title']) ?> event poster">
        </figure>

        <article class="event-information-panel">
            <span class="event-section-label">Event overview</span>
            <h2>About this event</h2>
            <p class="pre-line event-description"><?= e($event['description']) ?></p>
            <div class="event-facts">
                <div><small>Date</small><strong><?= e(date('l, F j, Y', strtotime($event['event_date']))) ?></strong></div>
                <div><small>Time</small><strong><?= e(date('g:i A', strtotime($event['start_time']))) ?><?= $event['end_time'] ? ' – '.e(date('g:i A', strtotime($event['end_time']))) : '' ?></strong></div>
                <div><small>Presented by</small><strong><?= e($event['organizer_name']) ?></strong></div>
                <div><small>Tickets remaining</small><strong><?= number_format((int)$event['available_tickets']) ?> / <?= number_format((int)$event['total_tickets']) ?></strong></div>
            </div>
        </article>

        <aside class="event-booking-panel">
        <div class="booking-panel-heading"><span>Tickets</span><small>Secure your place</small></div>
        <?php if ($ticketOptions === []): ?>
            <div class="large-price">LKR <?= number_format((float) $event['price'], 2) ?></div>
        <?php else: ?>
            <div class="ticket-option-list">
                <?php foreach ($ticketOptions as $option): ?>
                    <div class="ticket-option-item">
                        <strong><?= e($option['name']) ?></strong>
                        <span>LKR <?= number_format((float) $option['price'], 2) ?></span>
                        <small><?= number_format((int) $option['available_tickets']) ?> of <?= number_format((int) $option['total_tickets']) ?> left</small>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <?php if ($event['event_date'] < date('Y-m-d')): ?><div class="alert alert-error">This event has ended.</div><?php elseif (! $hasAvailability): ?><div class="alert alert-error">This event is sold out.</div><?php elseif (! $user): ?><a class="btn btn-primary full-width" href="<?= e(app_url('login', ['role'=>'customer'])) ?>">Sign in to book</a><p class="center muted">New customer? <a class="text-link" href="<?= e(app_url('register')) ?>">Register here</a></p><?php elseif ($user['role'] === 'customer'): ?><a class="btn btn-primary full-width" href="<?= e(app_url('book', ['id'=>$event['id']])) ?>">Book tickets</a><?php else: ?><p class="muted">Ticket booking is available to customer accounts.</p><?php endif; ?>
        <div class="booking-assurance"><span>✓</span><p><strong>Simple booking</strong><small>Your booking details are confirmed immediately.</small></p></div>
        </aside>
    </div>
</div>
</section>
<?php require __DIR__.'/includes/footer.php'; ?>

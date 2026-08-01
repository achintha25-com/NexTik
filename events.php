<?php

require __DIR__.'/includes/bootstrap.php';

$event = event_by_id((int) ($_GET['id'] ?? 0), true);
if (! $event) {
    http_response_code(404);
    render('error', ['title' => 'Event not found', 'message' => 'The requested event is not available.']);
    exit;
}

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
<section class="about-hero event-detail-hero"><div class="about-hero-backdrop" aria-hidden="true"></div><div class="container about-hero-layout"><div class="original-copy"><span class="original-kicker"><i></i> <?= e(strtoupper($event['category_name'])) ?></span><h1><?= e($event['title']) ?></h1><p><?= e($event['venue']) ?>, <?= e($event['city']) ?></p></div></div></section>
<section class="section"><div class="container detail-grid">
    <article class="detail-card"><div class="event-hero-art" style="background-image:linear-gradient(180deg,rgba(5,12,24,.12),rgba(5,12,24,.72)),url('<?= e(asset_url($eventImagePath)) ?>')"><span><?= e(date('M j', strtotime($event['event_date']))) ?></span></div><h2>About this event</h2><p class="pre-line"><?= e($event['description']) ?></p><div class="info-grid"><div><small>Date</small><strong><?= e(date('l, F j, Y', strtotime($event['event_date']))) ?></strong></div><div><small>Time</small><strong><?= e(date('g:i A', strtotime($event['start_time']))) ?><?= $event['end_time'] ? ' - '.e(date('g:i A', strtotime($event['end_time']))) : '' ?></strong></div><div><small>Organizer</small><strong><?= e($event['organizer_name']) ?></strong></div><div><small>Availability</small><strong><?= number_format((int)$event['available_tickets']) ?> of <?= number_format((int)$event['total_tickets']) ?></strong></div></div></article>
    <aside class="detail-card booking-sidebar"><p class="muted">Ticket price</p><div class="large-price">LKR <?= number_format((float)$event['price'], 2) ?></div>
        <?php if ($event['event_date'] < date('Y-m-d')): ?><div class="alert alert-error">This event has ended.</div><?php elseif ((int)$event['available_tickets'] < 1): ?><div class="alert alert-error">This event is sold out.</div><?php elseif (! $user): ?><a class="btn btn-primary full-width" href="<?= e(app_url('login', ['role'=>'customer'])) ?>">Sign in to book</a><p class="center muted">New customer? <a class="text-link" href="<?= e(app_url('register')) ?>">Register here</a></p><?php elseif ($user['role'] === 'customer'): ?><a class="btn btn-primary full-width" href="<?= e(app_url('book', ['id'=>$event['id']])) ?>">Book tickets</a><?php else: ?><p class="muted">Ticket booking is available to customer accounts.</p><?php endif; ?>
    </aside>
</div></section>
<?php require __DIR__.'/includes/footer.php'; ?>

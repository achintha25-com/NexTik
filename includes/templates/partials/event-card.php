<?php
$gradients = [
    'trending-now' => 'linear-gradient(145deg,#7f1d1d,#be123c)',
    'concerts' => 'linear-gradient(145deg,#4c1d95,#9333ea)',
    'edm' => 'linear-gradient(145deg,#1e3a8a,#2563eb)',
    'sport' => 'linear-gradient(145deg,#14532d,#16a34a)',
    'family' => 'linear-gradient(145deg,#9a3412,#ea580c)',
    'tamil-dj' => 'linear-gradient(145deg,#581c87,#c026d3)',
];
$gradient = $gradients[$event['category_slug']] ?? 'linear-gradient(145deg,#312e81,#be185d)';
$image = match ($event['category_slug']) {
    'concerts' => 'concert-card.png',
    'edm' => 'edm-card.png',
    default => 'hero-nextik.png',
};
$imagePath = ! empty($event['image']) ? 'images/'.ltrim((string) $event['image'], '/') : 'images/'.$image;
?>
<article class="event-card">
    <a href="<?= e(app_url('event', ['id' => $event['id']])) ?>" class="event-card-link">
        <div class="event-card-media" style="background-image:linear-gradient(180deg,rgba(5,12,24,.03) 25%,rgba(5,12,24,.84) 100%),url('<?= e(asset_url($imagePath)) ?>');background-color:<?= e($gradient) ?>">
            <?php if ($event['is_featured']): ?><span class="event-featured-tag">Featured</span><?php endif; ?>
            <div class="event-date-stamp"><span class="month"><?= e(date('M', strtotime($event['event_date']))) ?></span><span class="day"><?= e(date('j', strtotime($event['event_date']))) ?></span></div>
        </div>
        <div class="event-card-content">
            <p class="event-time"><?= e(date('g:i A', strtotime($event['start_time']))) ?> onwards</p>
            <h3><?= e($event['title']) ?></h3>
            <p class="event-venue"><?= e($event['venue']) ?>, <?= e($event['city']) ?></p>
            <div class="event-card-footer">
                <div class="price-tag"><small>Starting at</small>LKR <?= number_format((float) $event['price'], 2) ?></div>
                <?php if ((int) $event['available_tickets'] < 1): ?><span class="status-badge status-sold-out">Sold Out</span><?php else: ?><span class="btn btn-primary btn-sm">Book Now</span><?php endif; ?>
            </div>
        </div>
    </a>
</article>

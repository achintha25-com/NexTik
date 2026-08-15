<?php
$gradient = match ($event['category_slug'] ?? '') {
    'trending-now' => 'linear-gradient(145deg,#7f1d1d,#be123c)',
    'concerts' => 'linear-gradient(145deg,#4c1d95,#9333ea)',
    'edm' => 'linear-gradient(145deg,#1e3a8a,#2563eb)',
    'sport' => 'linear-gradient(145deg,#14532d,#16a34a)',
    'family' => 'linear-gradient(145deg,#9a3412,#ea580c)',
    'tamil-dj' => 'linear-gradient(145deg,#581c87,#c026d3)',
    default => 'linear-gradient(145deg,#312e81,#be185d)',
};
$imagePath = event_poster($event);
$ticketsLeft = (int) $event['available_tickets'];
$isSoldOut = $ticketsLeft < 1;
$isLowStock = ! $isSoldOut && $ticketsLeft <= 20;
$leadClass = ! empty($isLead) ? ' is-lead' : '';
?>
<article class="event-card<?= $leadClass ?>">
    <a href="<?= e(app_url('event', ['id' => $event['id']])) ?>" class="event-card-link">
        <div class="event-card-media" style="background-image:linear-gradient(180deg,rgba(5,12,24,.08) 20%,rgba(5,6,14,.88) 100%),url('<?= e(asset_url($imagePath)) ?>');background-color:<?= e($gradient) ?>">
            <?php if ($event['is_featured']): ?><span class="event-featured-tag">Featured</span><?php endif; ?>
            <?php if ($isLowStock): ?><span class="event-stock-tag">Only <?= $ticketsLeft ?> left</span><?php endif; ?>
            <div class="event-date-stamp"><span class="month"><?= e(date('M', strtotime($event['event_date']))) ?></span><span class="day"><?= e(date('j', strtotime($event['event_date']))) ?></span></div>
        </div>
        <div class="event-card-content">
            <p class="event-time"><?= e(event_countdown($event['event_date'])) ?> · <?= e(date('g:i A', strtotime($event['start_time']))) ?></p>
            <h3><?= e($event['title']) ?></h3>
            <p class="event-venue"><?= e($event['category_name']) ?> · <?= e($event['venue']) ?>, <?= e($event['city']) ?></p>
            <div class="event-card-footer">
                <div class="price-tag"><small>From</small>LKR <?= number_format((float) $event['price'], 0) ?></div>
                <?php if ($isSoldOut): ?><span class="status-badge status-sold-out">Sold Out</span><?php else: ?><span class="btn btn-primary btn-sm">Book Now</span><?php endif; ?>
            </div>
        </div>
    </a>
</article>

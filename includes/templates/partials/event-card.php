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
        <div class="event-card-media" style="background-image:linear-gradient(180deg,rgba(5,12,24,.02) 32%,rgba(5,6,14,.48) 100%),url('<?= e(asset_url($imagePath)) ?>'),<?= e($gradient) ?>">
        </div>
        <div class="event-card-content event-card-actions">
            <p class="event-time"><?= e(date('M j, Y', strtotime($event['event_date']))) ?> · <?= e(date('g:i A', strtotime($event['start_time']))) ?></p>
            <h3><?= e($event['title']) ?></h3>
            <div class="event-card-footer">
                <div class="price-tag"><small>From</small>LKR <?= number_format((float) $event['price'], 0) ?></div>
                <?php if ($isSoldOut): ?><span class="status-badge status-sold-out">Sold Out</span><?php else: ?><span class="btn btn-primary btn-sm">View &amp; Book</span><?php endif; ?>
            </div>
        </div>
    </a>
</article>

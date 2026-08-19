<?php
$today = date('Y-m-d');
$firstName = explode(' ', trim((string) ($user['name'] ?? 'there')))[0] ?: 'there';
$filterRoute = $filterRoute ?? 'bookings';
$filter = query_string('filter');
if (! in_array($filter, ['all', 'upcoming', 'past'], true)) {
    $filter = 'upcoming';
}

$upcoming = [];
$past = [];
$confirmedTickets = 0;
$confirmedSpend = 0.0;

foreach ($bookings as $booking) {
    $isPast = $booking['event_date'] < $today || $booking['status'] === 'cancelled';
    if ($isPast) {
        $past[] = $booking;
    } else {
        $upcoming[] = $booking;
    }

    if ($booking['status'] === 'confirmed') {
        $confirmedTickets += (int) $booking['quantity'];
        $confirmedSpend += (float) $booking['total_amount'];
    }
}

$past = array_reverse($past);
$visible = match ($filter) {
    'past' => $past,
    'all' => array_merge($upcoming, $past),
    default => $upcoming,
};
$next = $upcoming[0] ?? null;

$countdown = static function (string $date): string {
    $days = (int) round((strtotime($date) - strtotime(date('Y-m-d'))) / 86400);
    if ($days === 0) return 'Today';
    if ($days === 1) return 'Tomorrow';
    if ($days === -1) return 'Yesterday';
    if ($days > 1) return 'In '.$days.' days';

    return abs($days).' days ago';
};
?>

<section class="plain-page-head">
    <div class="container plain-page-head-inner">
        <div>
            <span class="plain-page-label">Your tickets</span>
            <h1>My bookings</h1>
            <?php if ($next): ?>
                <p>Next up: <?= e($next['event_title']) ?> · <?= e(date('M j', strtotime($next['event_date']))) ?> · <?= e($countdown($next['event_date'])) ?>.</p>
            <?php else: ?>
                <p>Your confirmed nights, seats, and booking passes live here.</p>
            <?php endif; ?>
        </div>
        <div class="plain-page-actions">
                <a class="btn btn-primary" href="<?= e(app_url()) ?>">Browse events</a>
                <a class="btn btn-secondary" href="<?= e(app_url('profile')) ?>">Account settings</a>
        </div>
    </div>
</section>

<section class="bookings-workspace">
    <div class="container">
        <div class="booking-metric-grid">
            <article class="booking-metric-card">
                <span>UP</span>
                <strong><?= count($upcoming) ?></strong>
                <small>Upcoming nights</small>
                <em><?= $next ? e($countdown($next['event_date'])) : 'Nothing locked in yet' ?></em>
            </article>
            <article class="booking-metric-card">
                <span>TK</span>
                <strong><?= number_format($confirmedTickets) ?></strong>
                <small>Tickets held</small>
                <em>Across confirmed bookings</em>
            </article>
            <article class="booking-metric-card booking-metric-spend">
                <span>LKR</span>
                <strong><?= number_format($confirmedSpend, 0) ?></strong>
                <small>Confirmed spend</small>
                <em><?= count($bookings) === 1 ? '1 booking' : count($bookings).' bookings' ?> in your history</em>
            </article>
        </div>

        <div class="booking-toolbar">
            <div>
                <p class="eyebrow">BOOKING PASSES</p>
                <h2><?= $filter === 'past' ? 'Past experiences' : ($filter === 'all' ? 'All bookings' : 'Coming up') ?></h2>
            </div>
            <nav class="booking-filters" aria-label="Filter bookings">
                <a class="<?= $filter === 'upcoming' ? 'is-active' : '' ?>" href="<?= e(app_url($filterRoute, ['filter' => 'upcoming'])) ?>">Upcoming <em><?= count($upcoming) ?></em></a>
                <a class="<?= $filter === 'past' ? 'is-active' : '' ?>" href="<?= e(app_url($filterRoute, ['filter' => 'past'])) ?>">Past <em><?= count($past) ?></em></a>
                <a class="<?= $filter === 'all' ? 'is-active' : '' ?>" href="<?= e(app_url($filterRoute, ['filter' => 'all'])) ?>">All <em><?= count($bookings) ?></em></a>
            </nav>
        </div>

        <?php if ($visible === []): ?>
            <div class="booking-empty">
                <?php if ($filter === 'upcoming' && $past !== []): ?>
                    <strong>No upcoming nights yet.</strong>
                    <p>Your past passes are saved. Browse what’s on next, or revisit a previous booking.</p>
                    <div class="actions-row">
                        <a class="btn btn-primary" href="<?= e(app_url()) ?>">Explore events</a>
                        <a class="btn btn-secondary" href="<?= e(app_url($filterRoute, ['filter' => 'past'])) ?>">View past bookings</a>
                    </div>
                <?php elseif ($bookings === []): ?>
                    <strong>Your first ticket starts here.</strong>
                    <p>Find a concert, festival, or night out and keep every pass in one place.</p>
                    <a class="btn btn-primary" href="<?= e(app_url()) ?>">Browse events</a>
                <?php else: ?>
                    <strong>Nothing in this view.</strong>
                    <p>Try another filter to see the rest of your NexTik bookings.</p>
                    <a class="btn btn-secondary" href="<?= e(app_url($filterRoute, ['filter' => 'all'])) ?>">Show all bookings</a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="ticket-pass-list">
                <?php foreach ($visible as $index => $booking): ?>
                    <?php
                    $isPast = $booking['event_date'] < $today || $booking['status'] === 'cancelled';
                    $poster = event_poster($booking);
                    ?>
                    <a class="ticket-pass<?= $isPast ? ' is-past' : '' ?>" href="<?= e(app_url('booking', ['id' => $booking['id']])) ?>" aria-label="View booking <?= e($booking['booking_reference']) ?> for <?= e($booking['event_title']) ?>" style="animation-delay: <?= number_format($index * 0.06, 2) ?>s">
                        <div class="ticket-pass-media" style="background-image:linear-gradient(180deg,rgba(5,6,14,.12),rgba(5,6,14,.78)),url('<?= e(asset_url($poster)) ?>')"></div>
                        <div class="ticket-pass-body">
                            <div class="ticket-pass-top">
                                <div class="ticket-pass-date">
                                    <strong><?= e(date('j', strtotime($booking['event_date']))) ?></strong>
                                    <span><?= e(date('M', strtotime($booking['event_date']))) ?></span>
                                </div>
                                <div class="ticket-pass-copy">
                                    <small><?= e($countdown($booking['event_date'])) ?> · <?= e($booking['category_name']) ?></small>
                                    <h3><?= e($booking['event_title']) ?></h3>
                                    <p><?= e($booking['venue']) ?>, <?= e($booking['city']) ?></p>
                                </div>
                                <span class="status-badge status-<?= e($booking['status']) ?>"><?= e(ucfirst($booking['status'])) ?></span>
                            </div>

                            <div class="ticket-pass-meta">
                                <div><small>Type</small><strong><?= e($booking['ticket_option_name'] ?: 'General') ?></strong></div>
                                <div><small>Tickets</small><strong><?= (int) $booking['quantity'] ?></strong></div>
                                <div><small>Starts</small><strong><?= e(date('g:i A', strtotime((string) $booking['start_time']))) ?></strong></div>
                                <div><small>Total</small><strong>LKR <?= number_format((float) $booking['total_amount'], 0) ?></strong></div>
                            </div>

                            <div class="ticket-pass-stub">
                                <span><?= e($booking['booking_reference']) ?></span>
                                <span class="ticket-pass-open">View pass <?= icon('view') ?></span>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

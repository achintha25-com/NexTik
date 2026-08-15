<?php
$today = date('Y-m-d');
$isPast = $booking['event_date'] < $today;
$isCancelled = $booking['status'] === 'cancelled';
$isPaid = ($booking['payment_status'] ?? '') === 'paid';
$poster = event_poster($booking);
$backUrl = ($user['role'] ?? '') === 'customer' ? app_url('bookings') : app_url('admin-dashboard');
$days = (int) round((strtotime((string) $booking['event_date']) - strtotime($today)) / 86400);
$countdown = match (true) {
    $days === 0 => 'Today',
    $days === 1 => 'Tomorrow',
    $days === -1 => 'Yesterday',
    $days > 1 => 'In '.$days.' days',
    default => abs($days).' days ago',
};
$heroLine = match (true) {
    $isCancelled => 'This reservation is no longer active.',
    $isPast => 'This night has already taken place.',
    default => $countdown.' · '.$booking['venue'].', '.$booking['city'],
};
$timeLabel = date('g:i A', strtotime((string) $booking['start_time']));
if (! empty($booking['end_time'])) {
    $timeLabel .= ' – '.date('g:i A', strtotime((string) $booking['end_time']));
}
?>

<section class="about-hero customer-hero">
    <div class="about-hero-backdrop" aria-hidden="true"></div>
    <div class="container about-hero-layout">
        <div class="original-copy">
            <span class="original-kicker"><i></i> BOOKING PASS</span>
            <h1><?= e($booking['event_title']) ?></h1>
            <p><?= e($heroLine) ?></p>
            <div class="original-actions no-print">
                <button class="btn btn-primary" type="button" onclick="window.print()">Print pass</button>
                <a class="original-link" href="<?= e($backUrl) ?>">Back to bookings <span>→</span></a>
            </div>
        </div>
    </div>
</section>

<section class="booking-pass-workspace">
    <div class="container booking-pass-layout">
        <article class="booking-board receipt">
            <div class="print-pass-brand">
                <span>Nex<span>Tik</span></span>
                <small>BOOKING PASS</small>
            </div>

            <div class="booking-board-media" style="background-image:linear-gradient(180deg,rgba(5,6,14,.08),rgba(5,6,14,.72)),url('<?= e(asset_url($poster)) ?>')">
                <div class="ticket-pass-date">
                    <strong><?= e(date('j', strtotime($booking['event_date']))) ?></strong>
                    <span><?= e(date('M', strtotime($booking['event_date']))) ?></span>
                </div>
            </div>

            <div class="booking-board-body">
                <div class="booking-board-top">
                    <div>
                        <small><?= e($booking['category_name']) ?> · <?= e($countdown) ?></small>
                        <h2><?= e($booking['event_title']) ?></h2>
                        <p><?= e($booking['venue']) ?>, <?= e($booking['city']) ?></p>
                    </div>
                    <div class="receipt-status">
                        <span class="status-badge status-<?= e($booking['status']) ?>"><?= e(ucfirst($booking['status'])) ?></span>
                        <?php if ($isPaid): ?>
                            <span class="status-badge status-confirmed">Paid</span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="booking-board-meta">
                    <div><small>Date</small><strong><?= e(date('l, F j, Y', strtotime($booking['event_date']))) ?></strong></div>
                    <div><small>Doors</small><strong><?= e($timeLabel) ?></strong></div>
                    <div><small>Ticket type</small><strong><?= e($booking['ticket_option_name'] ?: 'General') ?></strong></div>
                    <div><small>Tickets</small><strong><?= (int) $booking['quantity'] ?></strong></div>
                    <div><small>Guest</small><strong><?= e($booking['customer_name']) ?></strong></div>
                    <div><small>Total</small><strong>LKR <?= number_format((float) $booking['total_amount'], 2) ?></strong></div>
                </div>

                <div class="booking-board-stub">
                    <div>
                        <small>Booking reference</small>
                        <strong><?= e($booking['booking_reference']) ?></strong>
                    </div>
                    <div class="pass-barcode" aria-hidden="true">
                        <?php foreach (str_split((string) $booking['booking_reference']) as $character): ?>
                            <i style="width: <?= (ord($character) % 3) + 1 ?>px"></i>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </article>

        <aside class="booking-pass-side">
            <section class="booking-side-card">
                <p class="eyebrow">AT THE DOOR</p>
                <h3>Show this pass on entry.</h3>
                <p>Keep the reference handy. Print a copy or open this page when you arrive.</p>
                <div class="actions-row">
                    <button class="btn btn-primary" type="button" onclick="window.print()">Print booking pass</button>
                    <a class="btn btn-secondary" href="<?= e($backUrl) ?>">All bookings</a>
                </div>
            </section>

            <section class="booking-side-card">
                <p class="eyebrow">PAYMENT</p>
                <div class="booking-side-list">
                    <div><small>Amount</small><strong>LKR <?= number_format((float) $booking['total_amount'], 2) ?></strong></div>
                    <div><small>Method</small><strong><?= e(ucfirst((string) ($booking['payment_method'] ?? 'card'))) ?></strong></div>
                    <div><small>Status</small><strong><?= e(ucfirst((string) ($booking['payment_status'] ?: $booking['status']))) ?></strong></div>
                    <?php if (! empty($booking['payment_reference'])): ?>
                        <div><small>Payment ref</small><strong><?= e($booking['payment_reference']) ?></strong></div>
                    <?php endif; ?>
                    <?php if (! empty($booking['paid_at'])): ?>
                        <div><small>Paid on</small><strong><?= e(date('M j, Y · g:i A', strtotime((string) $booking['paid_at']))) ?></strong></div>
                    <?php endif; ?>
                </div>
            </section>

            <section class="booking-side-card">
                <p class="eyebrow">EVENT</p>
                <div class="booking-side-list">
                    <div><small>Email</small><strong><?= e($booking['customer_email']) ?></strong></div>
                    <div><small>Booked</small><strong><?= e(date('M j, Y', strtotime((string) $booking['created_at']))) ?></strong></div>
                </div>
                <a class="original-link" href="<?= e(app_url('event', ['id' => (int) $booking['event_id']])) ?>">View event page <span>→</span></a>
            </section>
        </aside>
    </div>
</section>

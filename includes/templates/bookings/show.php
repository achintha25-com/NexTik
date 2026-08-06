<section class="about-hero customer-hero">
    <div class="about-hero-backdrop" aria-hidden="true"></div>
    <div class="container about-hero-layout">
        <div class="original-copy">
            <span class="original-kicker"><i></i> BOOKING CONFIRMATION</span>
            <h1>Your spot is <span>reserved.</span></h1>
            <p><?= e($booking['booking_reference']) ?> · Your NexTik reservation details.</p>
        </div>
    </div>
</section>

<section class="section compact-top">
    <div class="container narrow">
        <div class="detail-card receipt">
            <div class="print-pass-brand">
                <span>Nex<span>Tik</span></span>
                <small>BOOKING PASS</small>
            </div>

            <div class="receipt-status">
                <span class="status-badge status-<?= e($booking['status']) ?>"><?= e(ucfirst($booking['status'])) ?></span>
                <?php if (! empty($booking['payment_status']) && $booking['payment_status'] === 'paid'): ?>
                    <span class="status-badge status-confirmed">Paid</span>
                <?php endif; ?>
            </div>

            <h2><?= e($booking['event_title']) ?></h2>
            <p class="muted"><?= e($booking['category_name']) ?></p>

            <div class="info-grid">
                <div><small>Date</small><strong><?= e(date('l, F j, Y', strtotime($booking['event_date']))) ?></strong></div>
                <div><small>Time</small><strong><?= e(date('g:i A', strtotime($booking['start_time']))) ?></strong></div>
                <div><small>Venue</small><strong><?= e($booking['venue']) ?>, <?= e($booking['city']) ?></strong></div>
                <div><small>Customer</small><strong><?= e($booking['customer_name']) ?></strong></div>
                <div><small>Ticket type</small><strong><?= e($booking['ticket_option_name'] ?: 'General') ?></strong></div>
                <div><small>Tickets</small><strong><?= (int) $booking['quantity'] ?></strong></div>
                <div><small>Total</small><strong>LKR <?= number_format((float) $booking['total_amount'], 2) ?></strong></div>
                <?php if (! empty($booking['payment_reference'])): ?>
                    <div><small>Payment ref</small><strong><?= e($booking['payment_reference']) ?></strong></div>
                    <div><small>Payment method</small><strong><?= e(ucfirst((string) ($booking['payment_method'] ?? 'card'))) ?></strong></div>
                <?php endif; ?>
            </div>

            <div class="actions-row top-gap no-print">
                <a class="btn btn-secondary" href="<?= e($user['role'] === 'customer' ? app_url('bookings') : app_url('admin-dashboard')) ?>">Back</a>
                <button class="btn btn-outline" type="button" onclick="window.print()">Print booking pass</button>
            </div>
        </div>
    </div>
</section>

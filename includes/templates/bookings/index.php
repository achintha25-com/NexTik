<section class="about-hero customer-hero">
    <div class="about-hero-backdrop" aria-hidden="true"></div>
    <div class="container about-hero-layout">
        <div class="original-copy">
            <span class="original-kicker"><i></i> CUSTOMER ACCOUNT</span>
            <h1>Your plans, <span>all in one place.</span></h1>
            <p>Review confirmations and manage your NexTik reservations.</p>
        </div>
    </div>
</section>

<section class="section compact-top">
    <div class="container">
        <div class="table-card">
            <table>
                <thead>
                    <tr>
                        <th>Reference</th>
                        <th>Event</th>
                        <th>Date</th>
                        <th>Tickets</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (! $bookings): ?>
                        <tr>
                            <td colspan="7" class="empty-state">
                                You have no bookings yet.
                                <a class="text-link" href="<?= e(app_url()) ?>">Explore events</a>.
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($bookings as $booking): ?>
                        <tr>
                            <td><strong><?= e($booking['booking_reference']) ?></strong></td>
                            <td>
                                <?= e($booking['event_title']) ?>
                                <small class="table-subtitle"><?= e($booking['venue']) ?>, <?= e($booking['city']) ?></small>
                            </td>
                            <td><?= e(date('M j, Y', strtotime($booking['event_date']))) ?></td>
                            <td><?= (int) $booking['quantity'] ?></td>
                            <td>LKR <?= number_format((float) $booking['total_amount'], 2) ?></td>
                            <td><span class="status-badge status-<?= e($booking['status']) ?>"><?= e(ucfirst($booking['status'])) ?></span></td>
                            <td><a class="btn btn-secondary btn-sm" href="<?= e(app_url('booking', ['id' => $booking['id']])) ?>">View</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

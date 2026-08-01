<section class="about-hero customer-hero">
    <div class="about-hero-backdrop" aria-hidden="true"></div>
    <div class="container about-hero-layout">
        <div class="original-copy">
            <span class="original-kicker"><i></i> CHECKOUT</span>
            <h1>Make it <span>official.</span></h1>
            <p><?= e($event['title']) ?></p>
        </div>
    </div>
</section>

<section class="section compact-top">
    <div class="container narrow">
        <div class="form-card">
            <?php require dirname(__DIR__).'/partials/errors.php'; ?>

            <div class="booking-summary">
                <div><small>Date</small><strong><?= e(date('M j, Y', strtotime($event['event_date']))) ?></strong></div>
                <div><small>Venue</small><strong><?= e($event['venue']) ?></strong></div>
                <div><small>Available</small><strong><?= number_format((int) $event['available_tickets']) ?> tickets</strong></div>
                <div><small>Unit price</small><strong>LKR <?= number_format((float) $event['price'], 2) ?></strong></div>
            </div>

            <?php if (! $errors || ((int) $event['available_tickets'] > 0 && $event['event_date'] >= date('Y-m-d'))): ?>
                <form method="post" action="<?= e(app_url('book', ['id' => $event['id']])) ?>">
                    <?= csrf_field() ?>
                    <div class="form-group">
                        <label for="quantity">Number of tickets (maximum 10)</label>
                        <input
                            class="form-control"
                            id="quantity"
                            name="quantity"
                            type="number"
                            min="1"
                            max="<?= min(10, (int) $event['available_tickets']) ?>"
                            value="<?= e($_POST['quantity'] ?? 1) ?>"
                            data-price="<?= e($event['price']) ?>"
                            data-total="booking-total"
                            required
                        >
                    </div>
                    <div class="total-line"><span>Total</span><strong id="booking-total"></strong></div>
                    <button class="btn btn-primary full-width" type="submit">Confirm booking</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</section>

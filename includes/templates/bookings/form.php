<section class="plain-page-head">
    <div class="container plain-page-head-inner">
        <div>
            <span class="plain-page-label">Checkout</span>
            <h1>Choose tickets</h1>
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
            </div>

            <?php if ($availableOptions !== [] && $event['event_date'] >= date('Y-m-d')): ?>
                <form method="post" action="<?= e(app_url('book', ['id' => $event['id']])) ?>">
                    <?= csrf_field() ?>
                    <div class="form-group">
                        <label for="ticket_option_id">Ticket option</label>
                        <select class="form-control" id="ticket_option_id" name="ticket_option_id" required data-booking-option>
                            <option value="">Choose an option</option>
                            <?php foreach ($ticketOptions as $option): ?>
                                <?php $isAvailable = (int) $option['available_tickets'] > 0; ?>
                                <option
                                    value="<?= (int) $option['id'] ?>"
                                    data-price="<?= e($option['price']) ?>"
                                    data-available="<?= (int) $option['available_tickets'] ?>"
                                    <?= ! $isAvailable ? 'disabled' : '' ?>
                                    <?= (int) ($_POST['ticket_option_id'] ?? 0) === (int) $option['id'] ? 'selected' : '' ?>
                                >
                                    <?= e($option['name']) ?> · LKR <?= number_format((float) $option['price'], 2) ?><?= $isAvailable ? ' ('.number_format((int) $option['available_tickets']).' left)' : ' (Sold out)' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="quantity">Number of tickets (maximum 10)</label>
                        <input
                            class="form-control"
                            id="quantity"
                            name="quantity"
                            type="number"
                            min="1"
                            max="10"
                            value="<?= e($_POST['quantity'] ?? 1) ?>"
                            data-total="booking-total"
                            required
                        >
                    </div>
                    <div class="total-line"><span>Total</span><strong id="booking-total">LKR 0.00</strong></div>
                    <button class="btn btn-primary full-width" type="submit">Continue to payment</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</section>

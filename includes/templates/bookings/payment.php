<section class="plain-page-head">
    <div class="container plain-page-head-inner">
        <div>
            <span class="plain-page-label">Secure payment</span>
            <h1>Complete payment</h1>
            <p><?= e($event['title']) ?> · <?= e($selectedOption['name']) ?></p>
        </div>
    </div>
</section>

<section class="section compact-top">
    <div class="container narrow">
        <div class="form-card payment-card">
            <?php require dirname(__DIR__).'/partials/errors.php'; ?>

            <div class="payment-summary">
                <div class="payment-summary-row">
                    <span>Ticket option</span>
                    <strong><?= e($selectedOption['name']) ?></strong>
                </div>
                <div class="payment-summary-row">
                    <span>Quantity</span>
                    <strong><?= (int) $pending['quantity'] ?></strong>
                </div>
                <div class="payment-summary-row">
                    <span>Unit price</span>
                    <strong>LKR <?= number_format((float) $selectedOption['price'], 2) ?></strong>
                </div>
                <div class="payment-summary-row payment-total">
                    <span>Amount due</span>
                    <strong>LKR <?= number_format((float) $pendingTotal, 2) ?></strong>
                </div>
            </div>

            <?php
            $paymentDefaults = [
                'card_name' => $user['name'] ?? 'Demo Customer',
                'card_number' => '4242 4242 4242 4242',
                'card_expiry' => '12/30',
                'card_cvv' => '123',
            ];
            ?>

            <form method="post" action="<?= e(app_url('book', ['id' => $event['id'], 'step' => 'pay'])) ?>" class="payment-form">
                <?= csrf_field() ?>

                <div class="form-group">
                    <label for="card_name">Name on card</label>
                    <input class="form-control" id="card_name" name="card_name" value="<?= e($_POST['card_name'] ?? $paymentDefaults['card_name']) ?>" maxlength="100" required autocomplete="cc-name">
                </div>

                <div class="form-group">
                    <label for="card_number">Card number</label>
                    <input class="form-control" id="card_number" name="card_number" value="<?= e($_POST['card_number'] ?? $paymentDefaults['card_number']) ?>" inputmode="numeric" maxlength="19" data-card-number required autocomplete="cc-number">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="card_expiry">Expiry</label>
                        <input class="form-control" id="card_expiry" name="card_expiry" value="<?= e($_POST['card_expiry'] ?? $paymentDefaults['card_expiry']) ?>" maxlength="5" data-card-expiry required autocomplete="cc-exp">
                    </div>
                    <div class="form-group">
                        <label for="card_cvv">CVV</label>
                        <input class="form-control" id="card_cvv" name="card_cvv" type="text" value="<?= e($_POST['card_cvv'] ?? $paymentDefaults['card_cvv']) ?>" inputmode="numeric" maxlength="4" required autocomplete="cc-csc">
                    </div>
                </div>

                <div class="payment-actions">
                    <button class="btn btn-primary full-width" type="submit">Pay LKR <?= number_format((float) $pendingTotal, 2) ?></button>
                    <a class="btn btn-secondary full-width" href="<?= e(app_url('book', ['id' => $event['id']])) ?>">Back to tickets</a>
                </div>
            </form>
        </div>
    </div>
</section>

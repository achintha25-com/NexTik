<?php
$options = $ticketOptions ?? [];
if ($options === []) {
    $options = [
        ['id' => 0, 'name' => 'VIP', 'price' => '', 'total_tickets' => ''],
        ['id' => 0, 'name' => 'Standing', 'price' => '', 'total_tickets' => ''],
    ];
}
?>
<div class="ticket-options-block">
    <div class="ticket-options-heading">
        <div>
            <label>Ticket options *</label>
            <p class="form-hint">Add choices like VIP, Standing, or General Admission. Each option has its own price and
                ticket count.</p>
        </div>
        <button class="btn btn-secondary btn-sm" type="button" data-add-ticket-option>Add option</button>
    </div>

    <div class="ticket-options-list" data-ticket-options>
        <?php foreach ($options as $option): ?>
            <div class="ticket-option-row">
                <input type="hidden" name="option_id[]" value="<?= (int) ($option['id'] ?? 0) ?>">
                <div class="form-group">
                    <label>Name</label>
                    <input class="form-control" name="option_name[]" value="<?= e($option['name'] ?? '') ?>" maxlength="100"
                        placeholder="VIP" required>
                </div>
                <div class="form-group">
                    <label>Price (LKR)</label>
                    <input class="form-control" name="option_price[]" type="number" min="0" step="0.01"
                        value="<?= e($option['price'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label>Total tickets</label>
                    <input class="form-control" name="option_total[]" type="number" min="1"
                        value="<?= e($option['total_tickets'] ?? '') ?>" required>
                </div>
                <button class="icon-btn icon-btn-danger ticket-option-remove" type="button" data-remove-ticket-option
                    title="Remove ticket option" aria-label="Remove ticket option"><?= icon('delete') ?></button>
            </div>
        <?php endforeach; ?>
    </div>
</div>
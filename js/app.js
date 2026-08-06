document.querySelectorAll('[data-password-toggle]').forEach((button) => {
    button.addEventListener('click', () => {
        const input = document.getElementById(button.dataset.passwordToggle);
        if (!input) return;
        const show = input.type === 'password';
        input.type = show ? 'text' : 'password';
        button.textContent = show ? 'Hide' : 'Show';
    });
});

document.querySelector('[data-menu-toggle]')?.addEventListener('click', () => {
    document.querySelector('[data-menu]')?.classList.toggle('is-open');
});

document.querySelectorAll('[data-confirm]').forEach((form) => {
    form.addEventListener('submit', (event) => {
        if (!window.confirm(form.dataset.confirm)) event.preventDefault();
    });
});

function updateBookingTotal() {
    const option = document.querySelector('[data-booking-option]');
    const quantity = document.querySelector('[data-total]');
    const output = document.getElementById(quantity?.dataset.total || 'booking-total');
    if (!option || !quantity || !output) return;

    const selected = option.options[option.selectedIndex];
    const price = Number(selected?.dataset.price || 0);
    const available = Number(selected?.dataset.available || 0);
    const maxTickets = Math.min(10, available || 10);

    quantity.max = maxTickets > 0 ? maxTickets : 1;
    if (Number(quantity.value || 0) > maxTickets) quantity.value = maxTickets > 0 ? maxTickets : 1;

    output.textContent = new Intl.NumberFormat('en-LK', {
        style: 'currency',
        currency: 'LKR',
        maximumFractionDigits: 2,
    }).format(Number(quantity.value || 0) * price);
}

document.querySelector('[data-booking-option]')?.addEventListener('change', updateBookingTotal);
document.querySelector('[data-total]')?.addEventListener('input', updateBookingTotal);
updateBookingTotal();

function ticketOptionRowHtml() {
    return `
        <div class="ticket-option-row">
            <input type="hidden" name="option_id[]" value="0">
            <div class="form-group">
                <label>Name</label>
                <input class="form-control" name="option_name[]" maxlength="100" placeholder="VIP" required>
            </div>
            <div class="form-group">
                <label>Price (LKR)</label>
                <input class="form-control" name="option_price[]" type="number" min="0" step="0.01" required>
            </div>
            <div class="form-group">
                <label>Total tickets</label>
                <input class="form-control" name="option_total[]" type="number" min="1" required>
            </div>
            <button class="btn btn-danger btn-sm ticket-option-remove" type="button" data-remove-ticket-option aria-label="Remove ticket option">&times;</button>
        </div>
    `;
}

document.querySelector('[data-add-ticket-option]')?.addEventListener('click', () => {
    const list = document.querySelector('[data-ticket-options]');
    if (!list) return;
    list.insertAdjacentHTML('beforeend', ticketOptionRowHtml());
});

document.addEventListener('click', (event) => {
    const button = event.target.closest('[data-remove-ticket-option]');
    if (!button) return;
    const row = button.closest('.ticket-option-row');
    const list = document.querySelector('[data-ticket-options]');
    if (!row || !list) return;
    if (list.querySelectorAll('.ticket-option-row').length <= 1) {
        window.alert('Keep at least one ticket option.');
        return;
    }
    row.remove();
});

function formatCardNumber(value) {
    return value.replace(/\D/g, '').slice(0, 16).replace(/(\d{4})(?=\d)/g, '$1 ').trim();
}

function formatCardExpiry(value) {
    const digits = value.replace(/\D/g, '').slice(0, 4);
    if (digits.length <= 2) return digits;
    return `${digits.slice(0, 2)}/${digits.slice(2)}`;
}

document.querySelector('[data-card-number]')?.addEventListener('input', (event) => {
    event.target.value = formatCardNumber(event.target.value);
});

document.querySelector('[data-card-expiry]')?.addEventListener('input', (event) => {
    event.target.value = formatCardExpiry(event.target.value);
});

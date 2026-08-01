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

document.querySelectorAll('[data-total]').forEach((quantity) => {
    const output = document.getElementById(quantity.dataset.total);
    const price = Number(quantity.dataset.price || 0);
    const update = () => {
        if (output) output.textContent = new Intl.NumberFormat('en-LK', {
            style: 'currency', currency: 'LKR', maximumFractionDigits: 2
        }).format(Number(quantity.value || 0) * price);
    };
    quantity.addEventListener('input', update);
    update();
});

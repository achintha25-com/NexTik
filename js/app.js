const loader = document.querySelector('[data-loader]');
const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
const hasSeenApp = sessionStorage.getItem('nextik-ready') === '1';
const hideLoader = () => {
    document.documentElement.classList.remove('is-loading');
    sessionStorage.setItem('nextik-ready', '1');
    if (!loader || loader.classList.contains('is-done')) return;
    loader.classList.add('is-done');
    loader.hidden = true;
    window.setTimeout(() => loader.remove(), 280);
};
const showLoaderIfSlow = () => {
    if (!loader || hasSeenApp || prefersReducedMotion || document.readyState === 'complete') return;
    loader.hidden = false;
    loader.classList.add('is-on');
    document.documentElement.classList.add('is-loading');
};
const slowTimer = window.setTimeout(showLoaderIfSlow, 400);
const finishLoad = () => {
    window.clearTimeout(slowTimer);
    hideLoader();
};
if (hasSeenApp || prefersReducedMotion || document.readyState === 'complete') finishLoad();
else window.addEventListener('load', finishLoad);
window.setTimeout(finishLoad, 2500);

const siteHeader = document.querySelector('[data-site-header]');
const syncHeader = () => siteHeader?.classList.toggle('is-scrolled', window.scrollY > 18);
syncHeader();
window.addEventListener('scroll', syncHeader, { passive: true });

document.querySelectorAll('[data-password-toggle]').forEach((button) => {
    button.addEventListener('click', () => {
        const input = document.getElementById(button.dataset.passwordToggle);
        if (!input) return;
        const show = input.type === 'password';
        input.type = show ? 'text' : 'password';
        button.textContent = show ? 'Hide' : 'Show';
        button.setAttribute('aria-pressed', show ? 'true' : 'false');
        button.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
    });
});

const menuToggle = document.querySelector('[data-menu-toggle]');
const siteMenu = document.querySelector('[data-menu]');
menuToggle?.addEventListener('click', () => {
    const isOpen = siteMenu?.classList.toggle('is-open');
    menuToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
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
            <button class="icon-btn icon-btn-danger ticket-option-remove" type="button" data-remove-ticket-option title="Remove ticket option" aria-label="Remove ticket option">
                <svg class="icon" viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 7h14"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M6 7l1 12a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2l1-12"/><path d="M9 7V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2"/></svg>
            </button>
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

function fireBookingConfetti() {
    if (!window.confetti) return;
    const colors = ['#ed1722', '#ff4b54', '#ffffff', '#fbbf24', '#fb7185'];
    const defaults = { colors, zIndex: 9999, disableForReducedMotion: true };

    window.confetti({ ...defaults, particleCount: 140, spread: 78, startVelocity: 48, origin: { y: 0.62 }, scalar: 1.05 });
    window.confetti({ ...defaults, particleCount: 40, spread: 100, scalar: 1.35, shapes: ['star'], origin: { y: 0.55 } });

    const end = Date.now() + 1600;
    const sides = () => {
        window.confetti({ ...defaults, particleCount: 4, angle: 60, spread: 55, origin: { x: 0 } });
        window.confetti({ ...defaults, particleCount: 4, angle: 120, spread: 55, origin: { x: 1 } });
        if (Date.now() < end) requestAnimationFrame(sides);
    };
    sides();
}

if (document.querySelector('[data-celebrate]') && !prefersReducedMotion) {
    const script = document.createElement('script');
    script.src = 'https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.3/dist/confetti.browser.min.js';
    script.async = true;
    script.onload = fireBookingConfetti;
    document.head.appendChild(script);
}

document.querySelectorAll('[data-tilt-card]').forEach((card) => {
    if (prefersReducedMotion) return;
    const shine = card.querySelector('.spotlight-shine');
    const reset = () => {
        card.classList.remove('is-tilting');
        card.style.setProperty('--tilt-x', '0deg');
        card.style.setProperty('--tilt-y', '0deg');
        card.style.setProperty('--tilt-lift', '0px');
        card.style.setProperty('--tilt-scale', '1');
        if (shine) shine.style.background = '';
    };
    card.addEventListener('mousemove', (event) => {
        const box = card.getBoundingClientRect();
        const x = (event.clientX - box.left) / Math.max(box.width, 1);
        const y = (event.clientY - box.top) / Math.max(box.height, 1);
        card.classList.add('is-tilting');
        card.style.setProperty('--tilt-x', `${((0.5 - y) * 16).toFixed(2)}deg`);
        card.style.setProperty('--tilt-y', `${((x - 0.5) * 20).toFixed(2)}deg`);
        card.style.setProperty('--tilt-lift', '-12px');
        card.style.setProperty('--tilt-scale', '1.04');
        if (shine) {
            shine.style.background = `radial-gradient(circle at ${Math.round(x * 100)}% ${Math.round(y * 100)}%, rgba(255,255,255,.4), transparent 55%)`;
        }
    });
    card.addEventListener('mouseleave', reset);
});

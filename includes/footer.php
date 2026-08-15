</main>

<footer class="site-footer">
    <div class="container footer-panel">
        <div class="footer-brand">
            <a href="<?= e(app_url()) ?>" class="logo">Nex<span>Tik</span></a>
            <p>Sri Lanka's ticket home for concerts, festivals, sport, and nights worth leaving the house for.</p>
            <a class="btn btn-primary btn-sm" href="<?= e(app_url()) ?>#events">Browse events</a>
        </div>
        <div>
            <h4>Explore</h4>
            <p><a href="<?= e(app_url()) ?>">All events</a></p>
            <p><a href="<?= e(app_url('about')) ?>">About NexTik</a></p>
            <p><a href="<?= e(app_url('contact')) ?>">Contact</a></p>
        </div>
        <div>
            <h4>Account</h4>
            <p><a href="<?= e(app_url('login', ['role' => 'customer'])) ?>">Customer login</a></p>
            <p><a href="<?= e(app_url('login', ['role' => 'organizer'])) ?>">Organizer login</a></p>
            <p><a href="<?= e(app_url('login', ['role' => 'admin'])) ?>">Admin login</a></p>
        </div>
        <div>
            <h4>Support</h4>
            <p><a href="<?= e(app_url('register')) ?>">Create an account</a></p>
            <p><a href="<?= e(app_url('bookings')) ?>">My bookings</a></p>
            <p><a href="mailto:hello@nextik.lk">hello@nextik.lk</a></p>
        </div>
    </div>
    <div class="container footer-bottom">
        <span>&copy; <?= date('Y') ?> NexTik. All rights reserved.</span>
        <span>Made for Sri Lanka's next night out.</span>
    </div>
</footer>
<script src="<?= e(asset_url('js/app.js')) ?>" defer></script>
</body>
</html>

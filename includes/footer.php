</main>

<footer class="site-footer">
    <div class="container footer-grid">
        <div>
            <div class="logo">Nex<span>Tik</span></div>
            <p>Sri Lanka's modern event ticket booking platform.</p>
        </div>
        <div>
            <h4>Explore</h4>
            <p><a href="<?= e(app_url()) ?>">Events</a></p>
            <p><a href="<?= e(app_url('about')) ?>">About us</a></p>
            <p><a href="<?= e(app_url('contact')) ?>">Contact</a></p>
        </div>
        <div>
            <h4>Account</h4>
            <p><a href="<?= e(app_url('login', ['role' => 'customer'])) ?>">Customer Login</a></p>
            <p><a href="<?= e(app_url('login', ['role' => 'organizer'])) ?>">Organizer Login</a></p>
            <p><a href="<?= e(app_url('login', ['role' => 'admin'])) ?>">Admin Login</a></p>
        </div>
    </div>
    <div class="container footer-bottom">&copy; <?= date('Y') ?> NexTik. All Rights Reserved.</div>
</footer>
<script src="<?= e(asset_url('js/app.js')) ?>" defer></script>
</body>

</html>
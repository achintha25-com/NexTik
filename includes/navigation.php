<nav class="nav-links" id="site-menu" data-menu>
    <a href="<?= e(app_url()) ?>">All Events</a>
    <a href="<?= e(app_url('about')) ?>">About</a>
    <a href="<?= e(app_url('contact')) ?>">Contact</a>
    <?php if ($user): ?>
        <?php if ($user['role'] === 'customer'): ?><a href="<?= e(app_url('bookings')) ?>">My Bookings</a><?php endif; ?>
        <?php if ($user['role'] === 'admin'): ?><a href="<?= e(app_url('admin-dashboard')) ?>">Dashboard</a><?php endif; ?>
        <?php if ($user['role'] === 'organizer'): ?><a href="<?= e(app_url('organizer-dashboard')) ?>">Dashboard</a><?php endif; ?>
        <a href="<?= e(app_url('profile')) ?>">Profile</a>
        <form method="post" action="<?= e(app_url('logout')) ?>" class="inline-form">
            <?= csrf_field() ?>
            <button class="btn btn-logout btn-sm" type="submit">Logout</button>
        </form>
    <?php else: ?>
        <a href="<?= e(app_url('login', ['role' => 'customer'])) ?>">Login</a>
        <a href="<?= e(app_url('register')) ?>" class="btn btn-primary btn-sm">Register</a>
    <?php endif; ?>
</nav>

<?php
$organizerPage = query_string('page') ?: 'organizer-dashboard';
$organizerLinks = [
    ['organizer-dashboard', 'Overview', '⌂'],
    ['organizer-events', 'My events', '◇'],
    ['organizer-event-form', 'Create event', '+'],
    ['profile', 'Profile settings', '◎'],
];
?>
<aside class="admin-sidebar organizer-sidebar">
    <div class="admin-sidebar-title"><span>Organizer panel</span><small>Event workspace</small></div>
    <nav aria-label="Organizer navigation">
        <?php foreach ($organizerLinks as [$route, $label, $symbol]): ?>
            <a class="<?= $organizerPage === $route ? 'active' : '' ?>" href="<?= e(app_url($route)) ?>">
                <span class="admin-nav-icon" aria-hidden="true"><?= e($symbol) ?></span><span><?= e($label) ?></span>
            </a>
        <?php endforeach; ?>
    </nav>
    <div class="admin-sidebar-account"><span><?= e(strtoupper(substr($user['name'] ?? 'O', 0, 1))) ?></span>
        <div><strong><?= e($user['name'] ?? 'Organizer') ?></strong><small>Event organizer</small></div>
    </div>
</aside>
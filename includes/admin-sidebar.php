<?php
$adminPage = query_string('page') ?: 'admin-dashboard';
$adminLinks = [
    ['admin-dashboard', 'Overview', '⌂', []],
    ['admin-users', 'Customers', '◎', ['role' => 'customer']],
    ['admin-users', 'Organizers', '◉', ['role' => 'organizer']],
    ['admin-users', 'Administrators', '●', ['role' => 'admin']],
    ['admin-events', 'Events', '◇', []],
    ['admin-categories', 'Categories', '▦', []],
    ['admin-messages', 'Messages', '✉', []],
    ['admin-reports', 'Reports', '▥', []],
    ['profile', 'Profile settings', '◎', []],
];
?>
<aside class="admin-sidebar">
    <div class="admin-sidebar-title"><span>Admin panel</span><small>Management workspace</small></div>
    <nav aria-label="Admin navigation">
        <?php foreach ($adminLinks as [$route, $label, $symbol, $parameters]): ?>
            <?php $isActive = $adminPage === $route && ($route !== 'admin-users' || query_string('role') === ($parameters['role'] ?? '')); ?>
            <a class="<?= $isActive ? 'active' : '' ?>" href="<?= e(app_url($route, $parameters)) ?>">
                <span class="admin-nav-icon" aria-hidden="true"><?= e($symbol) ?></span>
                <span><?= e($label) ?></span>
            </a>
        <?php endforeach; ?>
    </nav>
    <div class="admin-sidebar-account">
        <span><?= e(strtoupper(substr($user['name'] ?? 'A', 0, 1))) ?></span>
        <div><strong><?= e($user['name'] ?? 'Administrator') ?></strong><small>Administrator</small></div>
    </div>
</aside>

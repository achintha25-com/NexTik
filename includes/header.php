<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <title><?= e($title) ?> - NexTik</title>
    <script>document.documentElement.classList.add('js');</script>
    <link rel="stylesheet" href="<?= e(asset_url('css/style.css')) ?>?v=<?= (int) filemtime(dirname(__DIR__).'/css/style.css') ?>">
    <link rel="stylesheet" href="<?= e(asset_url('css/theme.css')) ?>?v=<?= (int) filemtime(dirname(__DIR__).'/css/theme.css') ?>">
</head>
<?php
$isAdminWorkspace = str_contains($bodyClass ?? '', 'admin-workspace');
$isOrganizerWorkspace = str_contains($bodyClass ?? '', 'organizer-workspace');
$isRoleWorkspace = $isAdminWorkspace || $isOrganizerWorkspace;
?>
<body class="<?= e($bodyClass ?? '') ?>">
<div class="site-loader" data-site-loader role="status" aria-live="polite" aria-label="Loading NexTik">
    <div class="site-loader-mark" aria-hidden="true">
        <span class="site-loader-ticket"></span>
        <strong>Nex<span>Tik</span></strong>
    </div>
    <div class="site-loader-track" aria-hidden="true"><span></span></div>
    <small>Finding your next experience</small>
</div>
<header class="site-header">
    <div class="container header-inner">
        <a href="<?= e(app_url()) ?>" class="nav-wordmark" aria-label="NexTik home">Nex<span>Tik</span></a>
        <span class="header-country" aria-label="Sri Lanka">
            <img class="header-flag" src="<?= e(asset_url('images/ui/sri-lanka-flag.svg')) ?>" alt="Sri Lankan flag">
        </span>
        <button class="menu-toggle" type="button" aria-label="Toggle menu" data-menu-toggle><span></span><span></span><span></span></button>
        <?php require __DIR__.'/navigation.php'; ?>
    </div>
</header>

<?php if ($isRoleWorkspace): ?>
<div class="admin-app-shell <?= $isOrganizerWorkspace ? 'organizer-app-shell' : '' ?>">
    <?php require __DIR__.($isOrganizerWorkspace ? '/organizer-sidebar.php' : '/admin-sidebar.php'); ?>
    <main class="admin-app-main role-app-main">
<?php else: ?>
<main>
<?php endif; ?>
    <?php if ($flashMessages): ?>
        <div class="container alert-stack">
            <?php foreach ($flashMessages as $type => $messages): ?>
                <?php foreach ($messages as $message): ?>
                    <div class="alert alert-<?= e($type) ?>"><?= e($message) ?></div>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

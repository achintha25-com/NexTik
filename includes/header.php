<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <title><?= e($title) ?> - NexTik</title>
    <link rel="stylesheet" href="<?= e(asset_url('css/style.css')) ?>">
</head>
<body class="<?= e($bodyClass ?? '') ?>">
<header class="site-header">
    <div class="container header-inner">
        <a href="<?= e(app_url()) ?>" class="logo">Nex<span>Tik</span></a>
        <button class="menu-toggle" type="button" aria-label="Toggle menu" data-menu-toggle>Menu</button>
        <?php require __DIR__.'/navigation.php'; ?>
    </div>
</header>

<main>
    <?php if ($flashMessages): ?>
        <div class="container alert-stack">
            <?php foreach ($flashMessages as $type => $messages): ?>
                <?php foreach ($messages as $message): ?>
                    <div class="alert alert-<?= e($type) ?>"><?= e($message) ?></div>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

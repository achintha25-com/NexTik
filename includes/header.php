<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <title><?= e($title) ?> - NexTik</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(asset_url('css/style.css')) ?>">
    <style>
        .site-loader{position:fixed;inset:0;z-index:5000;display:none;place-items:center;background:rgba(5,6,15,.72);backdrop-filter:blur(6px);transition:opacity .25s ease,visibility .25s ease}
        .site-loader.is-on{display:grid!important}
        .site-loader.is-done{opacity:0;visibility:hidden;pointer-events:none}
        html.is-loading,html.is-loading body{overflow:hidden}
        .skip-link{position:absolute;left:1rem;top:-4rem;z-index:6000;padding:.7rem 1rem;border-radius:999px;background:#ed1722;color:#fff;font-weight:700}
        .skip-link:focus{top:1rem}
        @media print{.site-loader{display:none!important}}
    </style>
    <noscript><style>.site-loader{display:none!important}</style></noscript>
</head>
<body class="<?= e($bodyClass ?? '') ?>">
<a class="skip-link" href="#main-content">Skip to content</a>
<div class="site-loader" data-loader role="status" aria-live="polite" aria-label="Loading" hidden>
    <span class="site-loader-ring" aria-hidden="true"></span>
</div>
<header class="site-header" data-site-header>
    <div class="container header-inner">
        <a href="<?= e(app_url()) ?>" class="logo">Nex<span>Tik</span></a>
        <button class="menu-toggle" type="button" aria-label="Toggle menu" aria-controls="site-menu" aria-expanded="false" data-menu-toggle>
            <span></span><span></span><span></span>
        </button>
        <?php require __DIR__.'/navigation.php'; ?>
    </div>
</header>

<main id="main-content">
    <?php if ($flashMessages): ?>
        <div class="container alert-stack">
            <?php foreach ($flashMessages as $type => $messages): ?>
                <?php foreach ($messages as $message): ?>
                    <div class="alert alert-<?= e($type) ?>"><?= e($message) ?></div>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

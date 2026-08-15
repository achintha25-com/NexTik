<?php

$spotlight = $featured[0] ?? $events[0] ?? null;
$selectedCategory = $category;
?>

<section class="original-hero">
    <div class="original-backdrop" aria-hidden="true"></div>
    <div class="container original-hero-layout">
        <div class="original-copy">
            <span class="original-kicker"><i></i> EVENTS MADE SIMPLE</span>
            <h1>Sri Lanka's best<br>moments, <span>one ticket away.</span></h1>
            <p>From live stages to family festivals, discover experiences worth leaving the house for.</p>

            <div class="original-actions">
                <a class="btn btn-primary" href="#events">Browse events</a>
                <a class="original-link" href="<?= e(app_url('about')) ?>">Meet NexTik <span>→</span></a>
            </div>

            <div class="original-stats">
                <div><strong><?= count($events) ?>+</strong><span>Upcoming events</span></div>
                <div><strong><?= count($categories) ?></strong><span>Event categories</span></div>
                <div><strong>100%</strong><span>Simple booking</span></div>
            </div>
        </div>

        <?php if ($spotlight): ?>
            <article class="spotlight-card">
                <div class="spotlight-top">
                    <span>Editor's pick</span>
                    <small><?= e(strtoupper($spotlight['category_name'])) ?></small>
                </div>
                <div class="spotlight-date">
                    <strong><?= e(date('j', strtotime($spotlight['event_date']))) ?></strong>
                    <span><?= e(date('M Y', strtotime($spotlight['event_date']))) ?></span>
                </div>
                <h2><?= e($spotlight['title']) ?></h2>
                <p><?= e($spotlight['venue']) ?> · <?= e($spotlight['city']) ?></p>
                <div class="spotlight-footer">
                    <span>From<strong>LKR <?= number_format((float) $spotlight['price'], 0) ?></strong></span>
                    <a href="<?= e(app_url('event', ['id' => $spotlight['id']])) ?>">View event →</a>
                </div>
            </article>
        <?php endif; ?>
    </div>

    <div class="finder-wrap">
        <div class="container">
            <form class="event-finder" method="get" action="<?= e(app_url()) ?>">
                <input type="hidden" name="page" value="home">
                <div class="finder-intro"><small>START HERE</small><strong>Find an event</strong></div>
                <label class="finder-field finder-keyword">
                    <span>⌕</span>
                    <span><small>Experience</small><input name="search" value="<?= e($search) ?>" placeholder="Concert, sport, festival..."></span>
                </label>
                <label class="finder-field">
                    <span>⌖</span>
                    <span><small>Location</small><select name="city"><option value="">Anywhere</option><?php foreach ($cities as $item): ?><option value="<?= e($item) ?>" <?= $city === $item ? 'selected' : '' ?>><?= e($item) ?></option><?php endforeach; ?></select></span>
                </label>
                <label class="finder-field">
                    <span>▣</span>
                    <span><small>Date</small><input name="date" type="date" value="<?= e($date) ?>"></span>
                </label>
                <button class="finder-button" type="submit">Explore <span>→</span></button>
            </form>
        </div>
    </div>
</section>

<section class="original-catalog" id="events">
    <div class="container">
        <div class="catalog-heading original-catalog-heading">
            <div><p class="catalog-eyebrow">CREATED FOR EVENT MOOD</p><h2>What's on next?</h2></div>
            <p>Fresh experiences, selected from across Sri Lanka.</p>
        </div>

        <div class="category-pills original-tabs">
            <a class="category-pill <?= $selectedCategory === '' ? 'active' : '' ?>" href="<?= e(app_url()) ?>">Discover all</a>
            <?php foreach ($categories as $item): ?>
                <a class="category-pill <?= $selectedCategory === $item['slug'] ? 'active' : '' ?>" href="<?= e(app_url('home', ['category' => $item['slug']])) ?>"><?= e($item['name']) ?></a>
            <?php endforeach; ?>
        </div>

        <div class="catalog-result-line"><span><?= count($events) ?> upcoming events</span><i></i></div>

        <?php if ($events): ?>
            <div class="events-grid original-events">
                <?php foreach ($events as $event): ?>
                    <?php require __DIR__.'/partials/event-card.php'; ?>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state-card"><h3>No events match your search.</h3><p>Try another category, date, or location.</p></div>
        <?php endif; ?>
    </div>
</section>

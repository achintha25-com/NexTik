<?php

$selectedCategory = $category;
$hasFilters = $search !== '' || $category !== '' || $city !== '' || $date !== '';
$baseFilters = array_filter([
    'search' => $search,
    'city' => $city,
    'date' => $date,
], static fn(string $value): bool => $value !== '');
$firstName = explode(' ', trim((string) ($user['name'] ?? '')))[0] ?? '';
$selectedCategoryName = '';
foreach ($categories as $item) {
    if ($item['slug'] === $category) {
        $selectedCategoryName = $item['name'];
        break;
    }
}
$catalogTitle = match (true) {
    $search !== '' => 'Results for “' . $search . '”',
    $selectedCategoryName !== '' => $selectedCategoryName . ' nights',
    $city !== '' => 'What’s on in ' . $city,
    $date !== '' => 'Events on ' . date('M j', strtotime($date)),
    default => "What's on next?",
};
?>

<section class="original-hero">
    <div class="original-backdrop" aria-hidden="true"></div>
    <div class="container original-hero-layout">
        <div class="original-copy">
            <h1>Find your next<br><span>event.</span></h1>
            <p>Discover events across Sri Lanka and book your tickets in a few simple steps.</p>
        </div>

        <aside class="home-hero-search" aria-label="Find an event">
            <div class="home-hero-search-heading">
                <small>Discover what is next</small>
                <h2>Search events</h2>
            </div>
            <form class="event-finder simple-event-search" method="get" action="<?= e(app_url()) ?>">
                <input type="hidden" name="page" value="home">
                <?php if ($category !== ''): ?><input type="hidden" name="category"
                        value="<?= e($category) ?>"><?php endif; ?>
                <label class="finder-field finder-keyword">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <circle cx="11" cy="11" r="6.5" />
                        <path d="m16 16 4 4" />
                    </svg>
                    <span><small>Search events</small><input name="search" value="<?= e($search) ?>"
                            placeholder="Event name or category"></span>
                </label>
                <label class="finder-field">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M12 21s7-5.4 7-11a7 7 0 1 0-14 0c0 5.6 7 11 7 11Z" />
                        <circle cx="12" cy="10" r="2.3" />
                    </svg>
                    <span><small>Location</small><select name="city">
                            <option value="">Anywhere</option><?php foreach ($cities as $item): ?>
                                <option value="<?= e($item) ?>" <?= $city === $item ? 'selected' : '' ?>><?= e($item) ?>
                                </option><?php endforeach; ?>
                        </select></span>
                </label>
                <label class="finder-field">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <rect x="3.5" y="5" width="17" height="15" rx="2" />
                        <path d="M8 3.5v3M16 3.5v3M3.5 10h17" />
                    </svg>
                    <span><small>Date</small><input name="date" type="date" value="<?= e($date) ?>"></span>
                </label>
                <button class="finder-button" type="submit">Search</button>
            </form>
        </aside>
    </div>
</section>

<section class="original-catalog" id="events">
    <div class="container">
        <div class="catalog-board">
            <div class="catalog-board-top">
                <div>
                    <p class="catalog-eyebrow"><?= $hasFilters ? 'Filtered for you' : 'This season' ?></p>
                    <h2><?= e($catalogTitle) ?></h2>
                    <p><?= $hasFilters ? 'Tune the chips or clear them to see the full calendar.' : 'Live nights across Sri Lanka, ready to book.' ?>
                    </p>
                </div>
                <div class="catalog-count">
                    <strong><?= count($events) ?></strong>
                    <span><?= count($events) === 1 ? 'event' : 'events' ?></span>
                </div>
            </div>

            <nav class="catalog-chips" aria-label="Event categories">
                <a class="<?= $selectedCategory === '' ? 'is-active' : '' ?>"
                    href="<?= e(app_url('home', $baseFilters)) ?>">
                    All <em><?= (int) $totalUpcoming ?></em>
                </a>
                <?php foreach ($categories as $item): ?>
                    <a class="<?= $selectedCategory === $item['slug'] ? 'is-active' : '' ?>"
                        href="<?= e(app_url('home', $baseFilters + ['category' => $item['slug']])) ?>">
                        <?= e($item['name']) ?> <em><?= (int) ($categoryCounts[$item['slug']] ?? 0) ?></em>
                    </a>
                <?php endforeach; ?>
            </nav>

            <?php if ($hasFilters): ?>
                <div class="home-filter-chips">
                    <?php if ($search !== ''): ?>
                        <a
                            href="<?= e(app_url('home', array_filter(['category' => $category, 'city' => $city, 'date' => $date]))) ?>">“<?= e($search) ?>”
                            ×</a>
                    <?php endif; ?>
                    <?php if ($selectedCategoryName !== ''): ?>
                        <a href="<?= e(app_url('home', $baseFilters)) ?>"><?= e($selectedCategoryName) ?> ×</a>
                    <?php endif; ?>
                    <?php if ($city !== ''): ?>
                        <a
                            href="<?= e(app_url('home', array_filter(['search' => $search, 'category' => $category, 'date' => $date]))) ?>"><?= e($city) ?>
                            ×</a>
                    <?php endif; ?>
                    <?php if ($date !== ''): ?>
                        <a
                            href="<?= e(app_url('home', array_filter(['search' => $search, 'category' => $category, 'city' => $city]))) ?>"><?= e(date('M j, Y', strtotime($date))) ?>
                            ×</a>
                    <?php endif; ?>
                    <a class="home-filter-clear" href="<?= e(app_url()) ?>">Clear all</a>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($events): ?>
            <div class="events-grid original-events">
                <?php foreach ($events as $index => $event): ?>
                    <?php $isLead = $index === 0 && !$hasFilters; ?>
                    <?php require __DIR__ . '/partials/event-card.php'; ?>
                <?php endforeach; ?>
            </div>
            <aside class="catalog-outro">
                <div>
                    <span class="catalog-outro-icon" aria-hidden="true">✦</span>
                    <p>YOUR NEXT STORY STARTS HERE</p>
                    <h2>Not sure what fits your mood?</h2>
                    <span>Tell us what you enjoy and we’ll help you find an experience worth remembering.</span>
                </div>
                <div class="catalog-outro-actions">
                    <a class="btn btn-primary" href="<?= e(app_url('contact')) ?>">Ask NexTik</a>
                    <a class="btn btn-secondary" href="#events">Browse again ↑</a>
                </div>
            </aside>
        <?php else: ?>
            <div class="booking-empty home-empty">
                <strong>No events match those filters.</strong>
                <p>Try another city, date, or category — or reset and browse the full season.</p>
                <div class="actions-row">
                    <a class="btn btn-primary" href="<?= e(app_url()) ?>">Show all events</a>
                    <a class="btn btn-secondary" href="<?= e(app_url('contact')) ?>">Ask NexTik</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>
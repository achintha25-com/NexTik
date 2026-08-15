<?php

$spotlight = $featured[0] ?? $events[0] ?? null;
$selectedCategory = $category;
$hasFilters = $search !== '' || $category !== '' || $city !== '' || $date !== '';
$baseFilters = array_filter([
    'search' => $search,
    'city' => $city,
    'date' => $date,
], static fn (string $value): bool => $value !== '');
$firstName = explode(' ', trim((string) ($user['name'] ?? '')))[0] ?? '';
$selectedCategoryName = '';
foreach ($categories as $item) {
    if ($item['slug'] === $category) {
        $selectedCategoryName = $item['name'];
        break;
    }
}
$catalogTitle = match (true) {
    $search !== '' => 'Results for “'.$search.'”',
    $selectedCategoryName !== '' => $selectedCategoryName.' nights',
    $city !== '' => 'What’s on in '.$city,
    $date !== '' => 'Events on '.date('M j', strtotime($date)),
    default => "What's on next?",
};
?>

<section class="original-hero">
    <div class="original-backdrop" aria-hidden="true"></div>
    <div class="container original-hero-layout">
        <div class="original-copy">
            <span class="original-kicker"><i></i> <?= $firstName !== '' ? 'WELCOME BACK, '.e(mb_strtoupper($firstName)) : 'LIVE ACROSS SRI LANKA' ?></span>
            <h1>Sri Lanka's best<br>moments, <span>one ticket away.</span></h1>
            <p>From live stages to family festivals, find the night, lock the seats, and keep every pass in one place.</p>

            <div class="original-actions">
                <a class="btn btn-primary" href="#events">Browse events</a>
                <a class="original-link" href="<?= e(app_url('about')) ?>">How NexTik works <span>→</span></a>
            </div>

            <div class="original-stats">
                <div><strong><?= count($events) ?>+</strong><span><?= $hasFilters ? 'Matching events' : 'Upcoming events' ?></span></div>
                <div><strong><?= count($categories) ?></strong><span>Event categories</span></div>
                <div><strong><?= count($cities) ?></strong><span>Cities this season</span></div>
            </div>
        </div>

        <?php if ($spotlight): ?>
            <div class="spotlight-tilt" data-tilt-card>
            <a class="spotlight-card" href="<?= e(app_url('event', ['id' => $spotlight['id']])) ?>">
                <span class="spotlight-shine" aria-hidden="true"></span>
                <div class="spotlight-media" style="background-image:linear-gradient(180deg,rgba(5,6,14,.12),rgba(5,6,14,.82)),url('<?= e(asset_url(event_poster($spotlight))) ?>')">
                    <span>Editor's pick</span>
                    <div class="spotlight-date">
                        <strong><?= e(date('j', strtotime($spotlight['event_date']))) ?></strong>
                        <em><?= e(date('M', strtotime($spotlight['event_date']))) ?></em>
                    </div>
                </div>
                <div class="spotlight-body">
                    <small><?= e(strtoupper($spotlight['category_name'])) ?> · <?= e(event_countdown($spotlight['event_date'])) ?></small>
                    <h2><?= e($spotlight['title']) ?></h2>
                    <p><?= e($spotlight['venue']) ?> · <?= e($spotlight['city']) ?></p>
                    <div class="spotlight-footer">
                        <span>From<strong>LKR <?= number_format((float) $spotlight['price'], 0) ?></strong></span>
                        <span class="spotlight-cta">View event →</span>
                    </div>
                </div>
            </a>
            </div>
        <?php endif; ?>
    </div>

    <div class="finder-wrap">
        <div class="container">
            <form class="event-finder" method="get" action="<?= e(app_url()) ?>">
                <input type="hidden" name="page" value="home">
                <?php if ($category !== ''): ?><input type="hidden" name="category" value="<?= e($category) ?>"><?php endif; ?>
                <div class="finder-intro"><small>START HERE</small><strong><?= $hasFilters ? 'Refine this search' : 'Find an event' ?></strong></div>
                <label class="finder-field finder-keyword">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="6.5"/><path d="m16 16 4 4"/></svg>
                    <span><small>Experience</small><input name="search" value="<?= e($search) ?>" placeholder="Concert, sport, festival..."></span>
                </label>
                <label class="finder-field">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 21s7-5.4 7-11a7 7 0 1 0-14 0c0 5.6 7 11 7 11Z"/><circle cx="12" cy="10" r="2.3"/></svg>
                    <span><small>Location</small><select name="city"><option value="">Anywhere</option><?php foreach ($cities as $item): ?><option value="<?= e($item) ?>" <?= $city === $item ? 'selected' : '' ?>><?= e($item) ?></option><?php endforeach; ?></select></span>
                </label>
                <label class="finder-field">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3.5" y="5" width="17" height="15" rx="2"/><path d="M8 3.5v3M16 3.5v3M3.5 10h17"/></svg>
                    <span><small>Date</small><input name="date" type="date" value="<?= e($date) ?>"></span>
                </label>
                <button class="finder-button" type="submit">Explore <span>→</span></button>
            </form>
        </div>
    </div>
</section>

<section class="original-catalog" id="events">
    <div class="container">
        <div class="home-trust">
            <article>
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7.5h16v11a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2z"/><path d="M8 7.5V5.8A2.8 2.8 0 0 1 10.8 3h2.4A2.8 2.8 0 0 1 16 5.8v1.7"/><path d="m9 13 2.1 2.1L15.5 11"/></svg>
                <div>
                    <strong>Instant tickets</strong>
                    <span>Digital passes land in your bookings the moment you pay.</span>
                </div>
            </article>
            <article>
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3 4.8 6.2v5.3c0 4.5 3.1 8.6 7.2 9.5 4.1-.9 7.2-5 7.2-9.5V6.2Z"/><path d="m9.2 12.1 1.9 1.9 3.8-4"/></svg>
                <div>
                    <strong>Official organizers</strong>
                    <span>Every listing is published by a verified NexTik organizer.</span>
                </div>
            </article>
            <article>
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 21s7-5.4 7-11a7 7 0 1 0-14 0c0 5.6 7 11 7 11Z"/><circle cx="12" cy="10" r="2.3"/></svg>
                <div>
                    <strong>Island-wide nights</strong>
                    <span>Concerts, sport, festivals, and family days from Colombo to Kandy.</span>
                </div>
            </article>
        </div>
        <div class="catalog-board">
            <div class="catalog-board-top">
                <div>
                    <p class="catalog-eyebrow"><?= $hasFilters ? 'Filtered for you' : 'This season' ?></p>
                    <h2><?= e($catalogTitle) ?></h2>
                    <p><?= $hasFilters ? 'Tune the chips or clear them to see the full calendar.' : 'Live nights across Sri Lanka, ready to book.' ?></p>
                </div>
                <div class="catalog-count">
                    <strong><?= count($events) ?></strong>
                    <span><?= count($events) === 1 ? 'event' : 'events' ?></span>
                </div>
            </div>

            <nav class="catalog-chips" aria-label="Event categories">
                <a class="<?= $selectedCategory === '' ? 'is-active' : '' ?>" href="<?= e(app_url('home', $baseFilters)) ?>">
                    All <em><?= (int) $totalUpcoming ?></em>
                </a>
                <?php foreach ($categories as $item): ?>
                    <a class="<?= $selectedCategory === $item['slug'] ? 'is-active' : '' ?>" href="<?= e(app_url('home', $baseFilters + ['category' => $item['slug']])) ?>">
                        <?= e($item['name']) ?> <em><?= (int) ($categoryCounts[$item['slug']] ?? 0) ?></em>
                    </a>
                <?php endforeach; ?>
            </nav>

            <?php if ($hasFilters): ?>
                <div class="home-filter-chips">
                    <?php if ($search !== ''): ?>
                        <a href="<?= e(app_url('home', array_filter(['category' => $category, 'city' => $city, 'date' => $date]))) ?>">“<?= e($search) ?>” ×</a>
                    <?php endif; ?>
                    <?php if ($selectedCategoryName !== ''): ?>
                        <a href="<?= e(app_url('home', $baseFilters)) ?>"><?= e($selectedCategoryName) ?> ×</a>
                    <?php endif; ?>
                    <?php if ($city !== ''): ?>
                        <a href="<?= e(app_url('home', array_filter(['search' => $search, 'category' => $category, 'date' => $date]))) ?>"><?= e($city) ?> ×</a>
                    <?php endif; ?>
                    <?php if ($date !== ''): ?>
                        <a href="<?= e(app_url('home', array_filter(['search' => $search, 'category' => $category, 'city' => $city]))) ?>"><?= e(date('M j, Y', strtotime($date))) ?> ×</a>
                    <?php endif; ?>
                    <a class="home-filter-clear" href="<?= e(app_url()) ?>">Clear all</a>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($events): ?>
            <div class="events-grid original-events">
                <?php foreach ($events as $event): ?>
                    <?php require __DIR__.'/partials/event-card.php'; ?>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="home-empty">
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

<section class="home-finale">
    <div class="container">
        <div class="home-finale-card">
            <div>
                <p class="catalog-eyebrow">Ready when you are</p>
                <h2><?= $user ? 'Your passes are waiting.' : 'Lock the night before it sells out.' ?></h2>
                <p><?= $user ? 'Open bookings anytime, print a pass, and walk in ready.' : 'Create a free account, keep every pass in one place, and skip the queue at the door.' ?></p>
            </div>
            <div class="actions-row">
                <?php if ($user && ($user['role'] ?? '') === 'customer'): ?>
                    <a class="btn btn-primary" href="<?= e(app_url('bookings')) ?>">My bookings</a>
                    <a class="original-link" href="#events">Keep browsing <span>→</span></a>
                <?php elseif ($user): ?>
                    <a class="btn btn-primary" href="<?= e(app_url('dashboard')) ?>">Open dashboard</a>
                    <a class="original-link" href="#events">Keep browsing <span>→</span></a>
                <?php else: ?>
                    <a class="btn btn-primary" href="<?= e(app_url('register')) ?>">Create free account</a>
                    <a class="original-link" href="<?= e(app_url('login', ['role' => 'customer'])) ?>">Sign in <span>→</span></a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

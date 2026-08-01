<?php

declare(strict_types=1);

require __DIR__.'/includes/bootstrap.php';

// Read the filters from the search form.
$search = query_string('search');
$category = query_string('category');
$city = query_string('city');

$conditions = [
    "e.status = 'published'",
    'e.event_date >= CURDATE()',
];
$parameters = [];

if ($search !== '') {
    $conditions[] = '(e.title LIKE ? OR e.venue LIKE ? OR e.city LIKE ?)';
    $term = '%'.$search.'%';
    array_push($parameters, $term, $term, $term);
}

if ($category !== '') {
    $conditions[] = 'c.slug = ?';
    $parameters[] = $category;
}

if ($city !== '') {
    $conditions[] = 'e.city = ?';
    $parameters[] = $city;
}

$sql = 'SELECT e.*, c.name AS category_name, c.slug AS category_slug, u.name AS organizer_name
        FROM events e
        JOIN categories c ON c.id = e.category_id
        JOIN users u ON u.id = e.organizer_id
        WHERE '.implode(' AND ', $conditions).'
        ORDER BY e.event_date, e.start_time';

$statement = db()->prepare($sql);
$statement->execute($parameters);
$events = $statement->fetchAll();

$categories = db()->query('SELECT name, slug FROM categories ORDER BY name')->fetchAll();
$cities = db()->query(
    "SELECT DISTINCT city
     FROM events
     WHERE status = 'published' AND event_date >= CURDATE()
     ORDER BY city"
)->fetchAll(PDO::FETCH_COLUMN);

$title = 'Search events';
$user = current_user();
$flashMessages = consume_flash();

require __DIR__.'/includes/header.php';
?>

<section class="hero">
    <div class="container">
        <p class="eyebrow">EVENT DISCOVERY</p>
        <h1>Find your next experience.</h1>
        <p>Search concerts, sports, EDM, family events, and more across Sri Lanka.</p>

        <form class="search-bar" method="get" action="<?= e(app_url('search')) ?>">
            <input type="hidden" name="page" value="search">
            <input type="search" name="search" value="<?= e($search) ?>" placeholder="Search events or venues">

            <select name="category">
                <option value="">All categories</option>
                <?php foreach ($categories as $item): ?>
                    <option value="<?= e($item['slug']) ?>" <?= $category === $item['slug'] ? 'selected' : '' ?>>
                        <?= e($item['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <select name="city">
                <option value="">All cities</option>
                <?php foreach ($cities as $item): ?>
                    <option value="<?= e($item) ?>" <?= $city === $item ? 'selected' : '' ?>>
                        <?= e($item) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button class="btn btn-primary" type="submit">Search</button>
        </form>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-heading">
            <div>
                <p class="eyebrow">YOUR RESULTS</p>
                <h2 class="section-title">Events that match your search</h2>
            </div>
            <span class="event-count"><?= count($events) ?> event<?= count($events) === 1 ? '' : 's' ?></span>
        </div>

        <?php if ($events === []): ?>
            <div class="empty-state">
                <h3>No events found</h3>
                <p>Try changing your keywords, category, or city.</p>
            </div>
        <?php else: ?>
            <div class="events-grid">
                <?php foreach ($events as $event): ?>
                    <?php require __DIR__.'/includes/templates/partials/event-card.php'; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require __DIR__.'/includes/footer.php'; ?>

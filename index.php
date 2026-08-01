<?php

declare(strict_types=1);

require __DIR__.'/includes/bootstrap.php';

// Homepage filters
$search = query_string('search');
$category = query_string('category');
$city = query_string('city');
$date = query_string('date');

$conditions = ["e.status = 'published'", 'e.event_date >= CURDATE()'];
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

if ($date !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
    $conditions[] = 'e.event_date = ?';
    $parameters[] = $date;
} else {
    $date = '';
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

$featured = ($search === '' && $category === '' && $city === '' && $date === '')
    ? array_values(array_filter($events, fn (array $event): bool => (bool) $event['is_featured']))
    : [];

$categories = db()->query('SELECT name, slug FROM categories ORDER BY name')->fetchAll();
$cities = db()->query("SELECT DISTINCT city FROM events WHERE status = 'published' AND event_date >= CURDATE() ORDER BY city")->fetchAll(PDO::FETCH_COLUMN);

// Shared page layout
$title = 'All events';
$bodyClass = 'home-page';
$user = current_user();
$flashMessages = consume_flash();

require __DIR__.'/includes/header.php';
require __DIR__.'/includes/templates/home.php';
require __DIR__.'/includes/footer.php';

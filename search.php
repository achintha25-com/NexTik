<?php

declare(strict_types=1);

require __DIR__ . '/includes/bootstrap.php';

$search = query_string('search');
$category = query_string('category');
$city = query_string('city');

$conditions = [
    "e.status = 'published'",
    'e.event_date >= CURDATE()',
];
$parameters = [];


<?php

require dirname(__DIR__).'/includes/bootstrap.php';

$user = require_role('customer');
render('bookings/index', [
    'title' => 'My bookings',
    'bodyClass' => 'customer-page bookings-page',
    'filterRoute' => 'bookings',
    'bookings' => customer_bookings((int) $user['id']),
]);

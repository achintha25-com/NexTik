<?php

require dirname(__DIR__).'/includes/bootstrap.php';

$user = require_role('customer');
render('bookings/index', [
    'title' => 'Customer dashboard',
    'bodyClass' => 'customer-page bookings-page',
    'filterRoute' => 'customer-dashboard',
    'bookings' => customer_bookings((int) $user['id']),
]);

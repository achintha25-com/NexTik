<?php

require dirname(__DIR__).'/includes/bootstrap.php';

$user = require_role('customer');
$statement = db()->prepare('SELECT b.*, e.title AS event_title, e.event_date, e.venue, e.city FROM bookings b JOIN events e ON e.id = b.event_id WHERE b.user_id = ? ORDER BY b.created_at DESC');
$statement->execute([$user['id']]);
render('bookings/index', ['title' => 'Customer dashboard', 'bookings' => $statement->fetchAll()]);

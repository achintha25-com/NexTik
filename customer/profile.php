<?php

require dirname(__DIR__).'/includes/bootstrap.php';

$user = require_role('customer');
$statement = db()->prepare('SELECT id, name, email, phone, role FROM users WHERE id = ?');
$statement->execute([$user['id']]);
render('profile', [
    'title' => 'Customer profile',
    'bodyClass' => 'profile-page',
    'profile' => $statement->fetch(),
    'errors' => [],
]);

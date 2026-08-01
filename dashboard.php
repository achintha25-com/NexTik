<?php

declare(strict_types=1);

require __DIR__.'/includes/bootstrap.php';

$user = require_login();

if ($user['role'] === 'admin') {
    redirect_to('admin-dashboard');
}

if ($user['role'] === 'organizer') {
    redirect_to('organizer-dashboard');
}

redirect_to('customer-dashboard');

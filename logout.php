<?php

require __DIR__ . '/includes/bootstrap.php';

if (is_post()) {
    verify_csrf();
    $_SESSION = [];
    session_regenerate_id(true);
    flash('success', 'You have been signed out.');
}
redirect_to('home');

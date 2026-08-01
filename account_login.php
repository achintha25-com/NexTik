<?php

require __DIR__.'/includes/bootstrap.php';

// Keep old bookmarks working after the account portal was combined with login.
redirect_to('login');

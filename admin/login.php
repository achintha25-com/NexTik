<?php

declare(strict_types=1);

require dirname(__DIR__).'/includes/bootstrap.php';

redirect_to('login', ['role' => 'admin']);

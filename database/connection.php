<?php

declare(strict_types=1);

return [
    'app_name' => 'NexTik',
    'timezone' => 'Asia/Colombo',
    'session_name' => 'nextik_session',
    'database' => [
        'host' => getenv('DB_HOST') ?: '127.0.0.1',
        'port' => getenv('DB_PORT') ?: '3306',
        'name' => getenv('DB_NAME') ?: 'event_booking_db',
        'user' => getenv('DB_USER') ?: 'root',
        'password' => getenv('DB_PASSWORD') ?: '',
        'charset' => 'utf8mb4',
    ],
];

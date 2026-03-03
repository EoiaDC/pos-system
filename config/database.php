<?php
return [
    'host'    => env_get('DB_HOST', 'localhost'),
    'port'    => env_get('DB_PORT', '3306'),
    'dbname'  => env_get('DB_DATABASE', 'pos_db'),  // Uses DB_DATABASE from .env
    'user'    => env_get('DB_USERNAME', 'root'),    // Uses DB_USERNAME from .env
    'pass'    => env_get('DB_PASSWORD', ''),
    'charset' => env_get('DB_CHARSET', 'utf8mb4'),
];
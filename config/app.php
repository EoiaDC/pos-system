<?php

return [
    'name'     => env_get('APP_NAME', 'POS Integration'),
    'env'      => env_get('APP_ENV', 'local'),
    'debug'    => env_bool('APP_DEBUG', true),
    'url'      => env_get('APP_URL', 'http://localhost/pos-system'),
    'timezone' => env_get('APP_TIMEZONE', 'Asia/Manila'),
];
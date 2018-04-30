<?php

/*
|--------------------------------------------------------------------------
| Example Databse Config
|--------------------------------------------------------------------------
|
| Place this file in your config/ directory. Rename it to "db.php"
|
*/

return [
    'driver' => env('DB_DRIVER', 'MySQLi'),
    'user'   => env('DB_USER', ''),
    'pass'   => env('DB_PASS', ''),
    'host'   => env('DB_HOST', ''),
    'name'   => env('DB_NAME', ''),
];

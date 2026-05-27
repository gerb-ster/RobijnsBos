<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__ . '/../robijnsbos_dt_app/vendor/autoload.php';

// Bootstrap Laravel and handle the request...
(require_once __DIR__ . '/../robijnsbos_dt_app/bootstrap/app.php')
    ->usePublicPath(base_path('../dt.robijnsbos.nl'))
    ->handleRequest(Request::capture());

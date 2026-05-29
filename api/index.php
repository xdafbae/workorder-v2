<?php

use Illuminate\Http\Request;

error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);

if ((getenv('APP_ENV') ?: 'production') === 'production') {
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
}

define('LARAVEL_START', microtime(true));

$tmpRoot = '/tmp/laravel';

foreach ([
    $tmpRoot,
    $tmpRoot.'/cache',
    $tmpRoot.'/sessions',
    $tmpRoot.'/views',
] as $path) {
    if (! is_dir($path)) {
        mkdir($path, 0777, true);
    }
}

$runtimeEnv = [
    'APP_CONFIG_CACHE' => $tmpRoot.'/cache/config.php',
    'APP_EVENTS_CACHE' => $tmpRoot.'/cache/events.php',
    'APP_PACKAGES_CACHE' => $tmpRoot.'/cache/packages.php',
    'APP_ROUTES_CACHE' => $tmpRoot.'/cache/routes.php',
    'APP_SERVICES_CACHE' => $tmpRoot.'/cache/services.php',
    'LOG_CHANNEL' => getenv('LOG_CHANNEL') ?: 'stderr',
    'VIEW_COMPILED_PATH' => getenv('VIEW_COMPILED_PATH') ?: $tmpRoot.'/views',
];

foreach ($runtimeEnv as $key => $value) {
    putenv($key.'='.$value);
    $_ENV[$key] = $value;
    $_SERVER[$key] = $value;
}

if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/../vendor/autoload.php';

(require_once __DIR__.'/../bootstrap/app.php')
    ->handleRequest(Request::capture());

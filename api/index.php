<?php

use Illuminate\Http\Request;

error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);

if ((getenv('APP_ENV') ?: 'production') === 'production') {
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
}

define('LARAVEL_START', microtime(true));

$tmpRoot = '/tmp/laravel';
$sqliteDatabase = $tmpRoot.'/database.sqlite';

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
    'APP_ENV' => getenv('APP_ENV') ?: 'production',
    'APP_CONFIG_CACHE' => $tmpRoot.'/cache/config.php',
    'APP_EVENTS_CACHE' => $tmpRoot.'/cache/events.php',
    'APP_PACKAGES_CACHE' => $tmpRoot.'/cache/packages.php',
    'APP_ROUTES_CACHE' => $tmpRoot.'/cache/routes.php',
    'APP_SERVICES_CACHE' => $tmpRoot.'/cache/services.php',
    'APP_MAINTENANCE_DRIVER' => getenv('APP_MAINTENANCE_DRIVER') ?: 'file',
    'BCRYPT_ROUNDS' => getenv('BCRYPT_ROUNDS') ?: '4',
    'CACHE_STORE' => getenv('CACHE_STORE') ?: 'array',
    'DB_CONNECTION' => getenv('DB_CONNECTION') ?: 'sqlite',
    'DB_DATABASE' => getenv('DB_DATABASE') ?: $sqliteDatabase,
    'LOG_CHANNEL' => getenv('LOG_CHANNEL') ?: 'stderr',
    'QUEUE_CONNECTION' => getenv('QUEUE_CONNECTION') ?: 'sync',
    'QUEUE_FAILED_DRIVER' => getenv('QUEUE_FAILED_DRIVER') ?: 'null',
    'SESSION_DRIVER' => getenv('SESSION_DRIVER') ?: 'cookie',
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

$app = require_once __DIR__.'/../bootstrap/app.php';

if (getenv('DB_CONNECTION') === 'sqlite' && getenv('DB_DATABASE') === $sqliteDatabase && ! file_exists($sqliteDatabase)) {
    touch($sqliteDatabase);

    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->call('migrate', ['--force' => true]);
    $kernel->call('db:seed', ['--force' => true]);
}

$app->handleRequest(Request::capture());

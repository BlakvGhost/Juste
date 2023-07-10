<?php

use Symfony\Component\Dotenv\Dotenv;

session_start();

define('DS', DIRECTORY_SEPARATOR);

define('BASE_URL', ROOT);

define('VIEW_PATH', BASE_URL  . DS . 'views');
define('ERRORS_VIEW_PATH', BASE_URL  . DS . 'views' . DS . 'errors');

define('CONTROLLERS_PATH', BASE_URL  . DS . 'Http' . DS . 'controllers');
define('MODELS_PATH', BASE_URL  . DS . 'Http' . DS . 'models');
define('UTILS_PATH', BASE_URL  . DS . 'Http' . DS . 'Utils');
define('ASSETS_PATH', BASE_URL  . DS . 'Assets');
define('UPLOAD_BASE_NAME', 'uploads');

$_BRAVO = [];

$dotenv = new Dotenv();

function dotEnvPath(): string
{
    $path = BASE_URL  . DS . '.env';
    if (!file_exists($path)) {
        $exemple_dotenv = BASE_URL . DS . 'env.exemple';
        if (file_exists($exemple_dotenv)) {
            $exemple_dotenv = file_get_contents($exemple_dotenv);
            file_put_contents($path, $exemple_dotenv);
        }
    }
    return $path;
}

$dotenv->loadEnv(dotEnvPath());

$current_user = isset($_SESSION['user']) ? $_SESSION['user'] : [];

require_once __DIR__ . '/Facades/Helpers/Autoload.php';

require_once BASE_URL . DS . 'routes' . DS . 'web.php';

loadRoute();

//dd($_BRAVO);

$payloads = getBravo('payloads');

if (!$payloads) {
    return require ERRORS_VIEW_PATH . DS . '404.php';
}

if ($payloads['type'] == 'html') {
    echo $payloads['html'];
    return;
}

loadView($payloads);

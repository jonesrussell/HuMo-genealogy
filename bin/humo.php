<?php

// Use absolute paths since we're in Docker
define('APP_ROOT', '/var/www/html');

require APP_ROOT . '/vendor/autoload.php';

use HumoGen\Core\Console\Application;

$app = new Application();
exit($app->run($argv)); 
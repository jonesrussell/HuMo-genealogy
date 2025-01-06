<?php

require __DIR__.'/../vendor/autoload.php';

use HumoGen\Core\Console\Application;

$app = new Application();
exit($app->run($argv)); 
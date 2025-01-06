<?php

/**
 * HuMo-genealogy - Modern Genealogy Application
 *
 * @link      https://humo-gen.com
 * @copyright Copyright (c) 2008-2024 HuMo-gen
 * @license   GNU GPL v3 or later
 */

declare(strict_types=1);

// Define the application root directory
define('ROOT_DIR', dirname(__DIR__));

// Composer autoloader
require ROOT_DIR . '/vendor/autoload.php';

// Bootstrap the application
$app = \HumoGen\Core\Application::getInstance();

// Legacy support - to be removed in future versions
require_once ROOT_DIR . '/include/db_login.php';
require_once ROOT_DIR . '/include/functions.php';
require_once ROOT_DIR . '/include/settings.php';

// Handle the request
try {
    // TODO: Implement proper routing
    if (isset($_GET['page'])) {
        $page = filter_var($_GET['page'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $file = ROOT_DIR . '/views/' . $page . '.php';
        
        if (file_exists($file)) {
            require $file;
        } else {
            throw new RuntimeException('Page not found', 404);
        }
    } else {
        require ROOT_DIR . '/views/home.php';
    }
} catch (Throwable $e) {
    if ($app->getService('config')->get('app.debug')) {
        throw $e;
    }
    
    // Log the error
    error_log($e->getMessage());
    
    // Show error page
    http_response_code($e->getCode() === 404 ? 404 : 500);
    require ROOT_DIR . '/views/error.php';
}

<?php

use HumoGen\Core\Application;

if (!function_exists('app')) {
    /**
     * Get the application instance.
     */
    function app(): Application
    {
        return Application::getInstance();
    }
} 
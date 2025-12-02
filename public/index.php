<?php

// 1. Composer autoload
require_once dirname(__DIR__) . '/vendor/autoload.php';

// 2. Load .env + config
use CMSOJ\Core\Config;
Config::load();

// 3. Initialize router + template
use CMSOJ\Router;
use CMSOJ\Template;

$router = new Router();

// 4. Load routes
require dirname(__DIR__) . '/CMSOJ/Routes/web.php';
require dirname(__DIR__) . '/CMSOJ/Routes/admin.php';

// 5. Dispatch
$router->dispatch();

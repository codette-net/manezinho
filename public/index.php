<?php

// Load autoloader (this loads all CMSOJ\* classes automatically)
require_once __DIR__ . '/../CMSOJ/autoload.php';

// Load config
CMSOJ\Core\Config::load();

// Start router
$router = new \CMSOJ\Router();

// Include PHPMailer library
require __DIR__ . '/../lib/phpmailer/Exception.php';
require __DIR__ . '/../lib/phpmailer/PHPMailer.php';
require __DIR__ . '/../lib/phpmailer/SMTP.php';

// Load route definitions
require __DIR__ . '/../CMSOJ/Routes/web.php';
require __DIR__ . '/../CMSOJ/Routes/admin.php';

// Dispatch request
$router->dispatch();

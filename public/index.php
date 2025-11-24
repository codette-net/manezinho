<?php
require_once __DIR__ . '/../CMSOJ/Template.php';
require_once __DIR__ . '/../CMSOJ/Router.php';

// Load routes
$router = new Router();
require __DIR__ . '/../CMSOJ/Routes/web.php';
require __DIR__ . '/../CMSOJ/Routes/admin.php';

// Dispatch current request
$router->dispatch();

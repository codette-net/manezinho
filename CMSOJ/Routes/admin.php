<?php

use CMSOJ\Router;
use CMSOJ\Middleware\AdminAuth;
use CMSOJ\Controllers\Admin\DashboardController;
use CMSOJ\Controllers\Admin\LoginController;
use CMSOJ\Controllers\Admin\LogoutController;

/** @var Router $router */

// LOGIN (public)
$router->get('admin/login', [LoginController::class, 'show']);
$router->post('admin/login', [LoginController::class, 'submit']);

// LOGOUT
$router->get('admin/logout', [LogoutController::class, 'logout']);

// DASHBOARD (protected)
$router->get('admin', [DashboardController::class, 'index'], AdminAuth::class);

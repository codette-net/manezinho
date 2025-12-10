<?php

use CMSOJ\Router;
use CMSOJ\Middleware\AdminAuth;
use CMSOJ\Controllers\Admin\DashboardController;
use CMSOJ\Controllers\Admin\AuthController;
use CMSOJ\Controllers\Admin\AccountsController;

/** @var Router $router */

// LOGIN (public)
$router->get('admin/login', [AuthController::class, 'show']);
$router->post('admin/login', [AuthController::class, 'submit']);

// LOGOUT
$router->get('admin/logout', [AuthController::class, 'logout']);

// DASHBOARD (protected)
$router->get('admin', [DashboardController::class, 'index'], AdminAuth::class);

// Accounts management (protected)
$router->get('admin/accounts', [AccountsController::class, 'index'], AdminAuth::class);
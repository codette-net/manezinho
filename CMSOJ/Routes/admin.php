<?php

use CMSOJ\Router;
use CMSOJ\Middleware\AdminAuth;
use CMSOJ\Middleware\AccountAuth;
use CMSOJ\Controllers\Admin\DashboardController;
use CMSOJ\Controllers\Admin\AuthController;
use CMSOJ\Controllers\Admin\AccountsController;
use CMSOJ\Controllers\Admin\SettingsController;
use CMSOJ\Controllers\Admin\MenuSectionController;
use CMSOJ\Controllers\Admin\MenuItemController;
use CMSOJ\Controllers\Admin\PageController;
use CMSOJ\Controllers\Admin\EventController;

/** @var Router $router */

// LOGIN (public)
$router->get('admin/login', [AuthController::class, 'show']);
$router->post('admin/login', [AuthController::class, 'submit']);

// LOGOUT
$router->get('admin/logout', [AuthController::class, 'logout']);

// DASHBOARD (protected)
$router->get('admin', [DashboardController::class, 'index'], AdminAuth::class);

// Accounts management (protected)
$router->get('admin/accounts/create', [AccountsController::class, 'create'], AccountAuth::class);
$router->post('admin/accounts/create', [AccountsController::class, 'store'], AccountAuth::class);
$router->get('admin/accounts', [AccountsController::class, 'index'], AccountAuth::class);
$router->get('admin/accounts/{id}', [AccountsController::class, 'index'], AccountAuth::class);

$router->get('admin/accounts/edit/{id}', [AccountsController::class, 'edit'], AccountAuth::class);
$router->post('admin/accounts/edit/{id}', [AccountsController::class, 'update'], AccountAuth::class);
$router->post('admin/accounts/bulk', [AccountsController::class, 'bulk'], AccountAuth::class);

$router->get('admin/profile', [AccountsController::class, 'profile'], AccountAuth::class);

// pages 
$router->get('admin/pages', [PageController::class, 'index'], AdminAuth::class);
$router->get('admin/pages/create', [PageController::class, 'create'], AdminAuth::class);
$router->get('admin/pages/edit/{id}', [PageController::class, 'edit'], AdminAuth::class);
$router->post('admin/pages/save', [PageController::class, 'save'], AdminAuth::class);
$router->post('admin/pages/delete/{id}', [PageController::class, 'delete'], AdminAuth::class);

// bulk optional
$router->post('admin/pages/bulk', [PageController::class, 'bulk'], AdminAuth::class);



// EVENTS (protected)
$router->get('admin/events', [EventController::class, 'index'], AdminAuth::class);
$router->get('admin/events/create', [EventController::class, 'create'], AdminAuth::class);
$router->get('admin/events/edit/{id}', [EventController::class, 'edit'], AdminAuth::class);
$router->post('admin/events/save', [EventController::class, 'save'], AdminAuth::class);
$router->post('admin/events/delete/{id}', [EventController::class, 'delete'], AdminAuth::class);
$router->post('admin/events/bulk', [EventController::class, 'bulk'], AdminAuth::class);


// Settings management (protected)
$router->get('admin/settings', [SettingsController::class, 'index'], AdminAuth::class);
$router->post('admin/settings', [SettingsController::class, 'save'], AdminAuth::class);

// restaurant menu sections management (protected)

$router->get('admin/menu/sections',[MenuSectionController::class, 'index'], AdminAuth::class
);

$router->post(
  'admin/menu/sections/delete/{id}',
  [MenuSectionController::class, 'delete'],
  AdminAuth::class
);

$router->post(
  'admin/menu/sections/update',
  [MenuSectionController::class, 'updateInline'],
  AdminAuth::class
);

$router->get(
    'admin/menu/sections/new',
    [MenuSectionController::class, 'create'],
    AdminAuth::class
);

$router->get(
    'admin/menu/sections/edit/{id}',
    [MenuSectionController::class, 'edit'],
    AdminAuth::class
);

$router->post(
    'admin/menu/sections/save',
    [MenuSectionController::class, 'save'],
    AdminAuth::class
);


$router->get(
    'admin/menu/items',
    [MenuItemController::class, 'index'],
    AdminAuth::class
);

$router->get(
    'admin/menu/items/create',
    [MenuItemController::class, 'create'],
    AdminAuth::class
);

$router->post(
    'admin/menu/items/store',
    [MenuItemController::class, 'store'],
    AdminAuth::class
);

$router->get(
    'admin/menu/items/edit/{id}',
    [MenuItemController::class, 'edit'],
    AdminAuth::class
);

$router->post(
    'admin/menu/items/update/{id}',
    [MenuItemController::class, 'update'],
    AdminAuth::class
);

$router->post(
    'admin/menu/items/update-inline',
    [MenuItemController::class, 'updateInline'],
    AdminAuth::class
);
<?php
namespace CMSOJ;
use CMSOJ\Template;
use CMSOJ\Controllers\Admin\DashboardController;
use CMSOJ\Middleware\AdminAuth;

$router->get('admin/test', [DashboardController::class, 'index'], AdminAuth::class);
// $router->get('admin/settings', [SettingsController::class, 'index'], AdminAuth::class);
// $router->post('admin/settings', [SettingsController::class, 'save'], AdminAuth::class);

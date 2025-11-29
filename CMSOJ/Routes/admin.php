<?php
namespace CMSOJ;
use CMSOJ\Template;

$router->get('admin', function() {
    Template::view('CMSOJ/Admin/Views/dashboard.html');
});

$router->get('admin/login', function() {
    Template::view('CMSOJ/Admin/Views/login.html');
});

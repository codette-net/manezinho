<?php

namespace CMSOJ\Controllers\Admin;

use CMSOJ\Template;

class DashboardController
{
    public function index()
    {
        return Template::view('CMSOJ/Views/admin/dashboard.html', [
            'title' => 'Admin Dashboard',
            'display_name' => $_SESSION['account_name'] ?? 'no_name',
        ]);
    }
}

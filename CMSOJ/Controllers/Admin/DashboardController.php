<?php

namespace CMSOJ\Controllers\Admin;

use CMSOJ\Template;

class DashboardController
{
    public function index()
    {
        // Temporary debug output
        echo "<h1>Admin dashboard is working!</h1>";
        exit;
            //     return Template::view('CMSOJ/Views/admin/dashboard.html', [
    //         'title' => 'Admin Dashboard',
    //         'display_name' => $_SESSION['account_name'] ?? 'no_name',
    //     ]);
    }
}

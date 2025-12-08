<?php
namespace CMSOJ\Middleware;

class AdminAuth
{
    public function handle()
    {
        // if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
        //     header("Location: /admin/login");
        //     exit;
        // }
        return true;
    }
}

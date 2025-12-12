<?php

namespace CMSOJ\Middleware;

// Accounts can only be viewed by admin users except for the users account themselves.
class AccountAuth
{
    public function handle()
    {
        session_start();

        if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
            header("Location: /admin/login");
            exit;
        }

        // Allow access if the user is an admin
        if (isset($_SESSION['admin_role']) && strtolower($_SESSION['admin_role']) === 'admin') {
            return;
        }

        // Extract account ID from the URL
        $requestId = $_GET['id'] ?? null;

        // Allow access if the user is accessing their own account
        if (isset($_SESSION['admin_id']) && $_SESSION['admin_id'] == $requestId) {
            return;
        }

        http_response_code(403);
        exit("You are not allowed to access this account.");
    }
}

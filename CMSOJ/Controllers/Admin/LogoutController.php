<?php

namespace CMSOJ\Controllers\Admin;

class LogoutController
{
    public function logout()
    {
        session_start();
        session_destroy();
        header("Location: /admin/login");
        exit;
    }
}

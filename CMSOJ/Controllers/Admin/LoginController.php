<?php

namespace CMSOJ\Controllers\Admin;

use CMSOJ\Core\Database;
use CMSOJ\Template;

class LoginController
{
    public function show()
    {
        return Template::view('CMSOJ/Views/admin/login.html', ['title' => 'Login', 'body_class' => 'login']);
    }

    public function submit()
    {
        session_start();

        $email = $_POST['admin_email'] ?? '';
        $password = $_POST['admin_password'] ?? '';

        $db = Database::connect();
        $stmt = $db->prepare("SELECT * FROM accounts WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $account = $stmt->fetch();

        if (!$account || !password_verify($password, $account['password'])) {
            $_SESSION['login_error'] = "Invalid login credentials.";
            header("Location: /admin/login");
            exit;
        }

        // Login OK
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_id'] = $account['id'];
        $_SESSION['admin_name'] = $account['name'];
        $_SESSION['account_name'] = $account['display_name'];


        header("Location: /admin");
        exit;
    }
}

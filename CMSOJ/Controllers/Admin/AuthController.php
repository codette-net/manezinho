<?php
namespace CMSOJ\Controllers\Admin;

use CMSOJ\Template;
use CMSOJ\Models\Account;

class AuthController
{
    public function showLogin()
    {
        return Template::view('CMSOJ/Views/admin/login.html');
    }

    public function login()
    {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $account = Account::findByEmail($email);

        if (!$account || !password_verify($password, $account['password'])) {
            return Template::view('CMSOJ/Views/admin/login.html', [
                'error' => 'Invalid login credentials'
            ]);
        }

        $_SESSION['admin_loggedin'] = true;
        $_SESSION['admin_id'] = $account['id'];

        header("Location: /admin");
        exit;
    }

    public function logout()
    {
        session_destroy();
        header("Location: /admin/login");
        exit;
    }
}

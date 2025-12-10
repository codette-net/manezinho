<?php

namespace CMSOJ\Controllers\Admin;

use CMSOJ\Template;
use CMSOJ\Models\Account;

class AuthController
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

    $email    = trim($_POST['admin_email'] ?? '');
    $password = trim($_POST['admin_password'] ?? '');

    // Validation
    if ($email === '' || $password === '') {
      $_SESSION['login_error'] = "Please fill in all fields.";
      header("Location: /admin/login");
      exit;
    }

    $account = Account::find($email);

    if (!$account || !password_verify($password, $account['password'])) {
      $_SESSION['login_error'] = "Invalid login credentials.";
      $this->show();
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
  public function logout()
  {
    session_start();
    session_destroy();
    header("Location: /admin/login");
    exit;
  }
}

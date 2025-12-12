<?php

namespace CMSOJ\Controllers\Admin;

use CMSOJ\Controllers\Admin\AuthController;
use CMSOJ\Models\Account;
use CMSOJ\Template;

class AccountsController
{
    public function index()
    {
        // only users with admin role can view/edit all accounts
        if (strtolower($_SESSION['admin_role']) === 'admin') {
            $accounts = (new Account())->all();
        } else {
            $accounts = [(new Account())->find($_SESSION['admin_id'])];
        }
        $rows = array_map(function ($a) {
            return [
                $a['id'],
                $a['email'],
                $a['display_name'],
                $a['role'],
                "<a href='/admin/accounts/edit/{$a['id']}'>Edit</a>"
            ];
        }, $accounts);

        return Template::view('CMSOJ/Views/admin/accounts/index.html', [
            'headers' => ["ID", "Email", "Display Name", "Role", "Actions"],
            'rows'    => $rows,
            'title'   => 'Accounts'
        ]);
    }

    public function profile()
    {
        $account = (new Account())->find($_SESSION['admin_id']);

        return Template::view('CMSOJ/Views/admin/accounts/profile.html', [
            'title' => 'Profile',
            'account' => $account
        ]);
    }


    public function edit($id)
    {
        $account = (new Account())->find((int)$id);

        $isAdmin = strtolower($_SESSION['admin_role']) === 'admin';
        $isSelf  = $account['id'] == $_SESSION['admin_id'];

        if (!$isAdmin && !$isSelf) {
            http_response_code(403);
            exit("You are not allowed to edit this account.");
        }


        return Template::view('CMSOJ/Views/admin/accounts/edit.html',['title' => 'Edit Accounts', 'account' => $account]);
    }

    public function update($id)
    {
        $data = [
            'name'         => $_POST['name'],
            'display_name' => $_POST['display_name'],
            'email'        => $_POST['email'],
            'password'     => $_POST['password'] ?? null
        ];

        (new Account())->update((int)$id, $data);



        if (isset($data['password']) && !empty($data['password'])) {
            // If the user updated their own password, log them out
            if ($id == $_SESSION['admin_id']) {
                (new AuthController())->logout();
            }
        }

        $_SESSION['flash_success'] = "Account updated successfully.";

        header("Location: /admin/accounts");
        exit;
    }
}

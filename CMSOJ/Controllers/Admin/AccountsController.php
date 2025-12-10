<?php

namespace CMSOJ\Controllers\Admin;

use CMSOJ\Models\Account;
use CMSOJ\Template;

class AccountsController
{
    public function index()
    {
        $accounts = Account::all();
        return Template::view('CMSOJ/Views/admin/accounts/index.html', compact('accounts'));
    }

    public function edit($id)
    {
        $account = Account::find((int) $id);

        if (!$account) {
            http_response_code(404);
            exit("Account not found.");
        }

        return Template::view('CMSOJ/Views/admin/accounts/edit.html', compact('account'));
    }

    public function update($id)
    {
        $data = [
            'name'         => $_POST['name'],
            'display_name' => $_POST['display_name'],
            'email'        => $_POST['email'],
            'password'     => $_POST['password'] ?? null
        ];

        Account::update((int)$id, $data);

        header("Location: /admin/accounts");
        exit;
    }
}

<?php

namespace CMSOJ\Controllers\Admin;

use CMSOJ\Controllers\Admin\AuthController;
use CMSOJ\Models\Account;
use CMSOJ\Template;
use CMSOJ\Helpers\Permissions;
use CMSOJ\Helpers\Validator;
use CMSOJ\Helpers\Redirect;
use CMSOJ\Helpers\Flash;

class AccountsController
{
    public function index()
    {
        if (Permissions::can('accounts.view_all')) {
            $result = (new Account())->list([
                'columns' => ['id', 'name', 'email', 'display_name', 'role', 'updated_at', 'last_seen'],
                'sort'    => 'id',
                'dir'     => 'asc',
                'page'    => (int)$_GET['page'] ?? 1,
                'perPage' => 5,
            ]);

            $accounts = $result['data'];
            $meta     = $result['meta'];
        } else {
            $accounts = [(new Account())->find($_SESSION['admin_id'])];
            $meta = null;
        }

        $rows = array_map(function ($a) {
            return [
                $a['id'],
                $a['name'],
                $a['email'],
                $a['display_name'],
                $a['role'],
                date('Y-m-d H:i', strtotime($a['updated_at'] ?? '')),
                date('Y-m-d H:i', strtotime($a['last_seen'] ?? '')),
                "<a href='/admin/accounts/edit/{$a['id']}'>Edit</a>"
            ];
        }, $accounts);

        return Template::view('CMSOJ/Views/admin/accounts/index.html', [
            'headers' => [
                "ID",
                "Name",
                "Email",
                "Display Name",
                "Role",
                "Last Updated",
                "Last Seen",
                "Actions"
            ],
            'rows'  => $rows,
            'meta'  => $meta,
            'query' => $_GET,
            'title' => 'Accounts'
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

    public function create()
    {
        // only admins can create accounts
        if (!Permissions::can('accounts.create')) {
            http_response_code(403);
            exit("You are not allowed to create accounts.");
        }
        return Template::view('CMSOJ/Views/admin/accounts/create.html', ['title' => 'Create Account']);
    }

    public function store()
    {
        if (!Permissions::can('accounts.create')) {
            http_response_code(403);
            exit('Not allowed');
        }

        $validator = Validator::make($_POST, [
            'name'         => 'required',
            'display_name' => 'required',
            'email'        => 'required|email',
            'password'     => 'required|min:8',
            'role'         => 'required',
        ]);

        if ($validator->fails()) {
            return Redirect::back()
                ->withErrors($validator)
                ->withOld()
                ->send();
        }

        $data = [
            'name'         => $_POST['name'],
            'display_name' => $_POST['display_name'],
            'email'        => $_POST['email'],
            'password'     => password_hash($_POST['password'], PASSWORD_DEFAULT),
            'role'         => $_POST['role'],
            'updated_at'   => date('Y-m-d H:i:s'),
            'last_seen'    => null
        ];

        (new Account())->create($data);

        Flash::set('success', 'Account created successfully.');
        header("Location: /admin/accounts");
        exit;
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


        return Template::view('CMSOJ/Views/admin/accounts/edit.html', ['title' => 'Edit Accounts', 'account' => $account]);
    }

    public function update($id)
    {
        $account = (new Account())->find($id);

        // Permissions
        $isAdmin = Permissions::can('accounts.edit_other');
        $isSelf  = $_SESSION['admin_id'] == $id;

        if (!$isAdmin && !$isSelf) {
            http_response_code(403);
            exit("Unauthorized");
        }

        $validator = Validator::make($_POST, [
            'name'         => 'required',
            'display_name' => 'required',
            'email'        => 'required|email',
            'password'     => 'nullable|min:8',
        ]);

        if ($validator->fails()) {
            return Redirect::back()
                ->withErrors($validator)
                ->withOld()
                ->send();
        }

        $data = [
            'name'         => $_POST['name'],
            'display_name' => $_POST['display_name'],
            'email'        => $_POST['email'],
            'updated_at'   => date('Y-m-d H:i:s'),
        ];

        if (!empty($_POST['password'])) {
            $data['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);

            if ($isSelf) {
                (new AuthController())->logout();
            }
        }

        if ($isAdmin) {
            $data['role'] = $_POST['role'];
        }

        (new Account())->update($id, $data);

        Flash::set('success', 'Account updated successfully.');
        header("Location: /admin/accounts");
        exit;
    }
}

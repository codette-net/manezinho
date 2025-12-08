<?php 
namespace CMSOJ\Controllers\Admin;

use CMSOJ\Template;
use CMSOJ\Models\Account;

class AccountsController
{
    public function index()
    {
        $accounts = Account::all();
        return Template::view('CMSOJ/Views/admin/accounts/index.html', compact('accounts'));
    }

    public function edit($id)
    {
        $account = Account::find($id);
        return Template::view('CMSOJ/Views/admin/accounts/edit.html', compact('account'));
    }
}

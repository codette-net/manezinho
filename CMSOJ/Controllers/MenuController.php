<?php

namespace CMSOJ\Controllers;

use CMSOJ\Services\MenuService;
use CMSOJ\Template;

class MenuController
{
    private MenuService $service;

    public function __construct()
    {
        $this->service = new MenuService();
    }

    public function index()
    {
        $lang = ($_GET['lang'] ?? 'en') === 'pt' ? 'pt' : 'en';
        $data = $this->service->getMenu($lang);

        Template::view('CMSOJ/Views/menu.html', $data);
    }
}

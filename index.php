<?php
include 'CMSOJ/Template.php';
include 'CMSOJ/Router.php';

$router = new Router();

// Home
$router->get('', function() {
    Template::view('CMSOJ/Views/index.html');
});

// About page
$router->get('about', function() {
    Template::view('CMSOJ/Views/about.html');
});

// Dynamic blog post: /blog/123
$router->get('blog/{id}', function($id) {
    Template::view('CMSOJ/Views/blog.html', ['id' => $id]);
});

// Dispatch the matched route
$router->dispatch();

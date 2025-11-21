<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// -------------------------------------------------------
// 🔹 Default Route
// -------------------------------------------------------
$routes->get('/', 'Home::index');

// -------------------------------------------------------
// 🔹 Browser Routes (for admin / testing in browser)
// -------------------------------------------------------
$routes->get('browser', 'Home::index');
$routes->get('browser/products', 'Home::products');
$routes->get('browser/products/view/(:num)', 'Home::productDetail/$1');
$routes->get('browser/categories', 'Home::categories');
$routes->get('browser/orders', 'Home::orders');

// -------------------------------------------------------
// 🔹 Admin Auth Routes (public: login / attempt / logout)
// -------------------------------------------------------
$routes->get('admin/login', '\App\Controllers\Admin\Auth::login');
$routes->post('admin/auth/attempt', '\App\Controllers\Admin\Auth::attempt');
$routes->get('admin/logout', '\App\Controllers\Admin\Auth::logout');

// -------------------------------------------------------
// 🔹 Admin Routes (server rendered admin UI - protected)
// -------------------------------------------------------
$routes->group(
    'admin',
    ['namespace' => 'App\Controllers\Admin', 'filter' => 'adminAuth'],
    function ($routes) {

        // Dashboard & root
        $routes->get('', 'Dashboard::index');
        $routes->get('dashboard', 'Dashboard::index');

        // Orders (admin UI)
        $routes->get('orders', 'Orders::index');
        $routes->get('orders/view/(:num)', 'Orders::view/$1');
        $routes->post('orders/changeStatus/(:num)', 'Orders::changeStatus/$1');

        // Users
        $routes->get('users', 'Users::index');
        $routes->get('users/create', 'Users::create');
        $routes->post('users/store', 'Users::store');
        $routes->get('users/edit/(:num)', 'Users::edit/$1');
        $routes->post('users/update/(:num)', 'Users::update/$1');
        $routes->post('users/delete/(:num)', 'Users::delete/$1');

        // Categories (admin UI)
        $routes->get('categories', 'Categories::index');
        $routes->get('categories/create', 'Categories::create');
        $routes->post('categories/store', 'Categories::store');
        $routes->get('categories/edit/(:num)', 'Categories::edit/$1');
        $routes->post('categories/update/(:num)', 'Categories::update/$1');
        $routes->post('categories/delete/(:num)', 'Categories::delete/$1');

        // Uploads
        $routes->get('uploads', 'Uploads::index');
        $routes->post('uploads/store', 'Uploads::store');
        $routes->post('uploads/delete/(:num)', 'Uploads::delete/$1');

        // Products (admin UI)
        $routes->get('products', 'Products::index');
        $routes->get('products/create', 'Products::create');
        $routes->post('products/store', 'Products::store');
        $routes->get('products/edit/(:num)', 'Products::edit/$1');
        $routes->post('products/update/(:num)', 'Products::update/$1');
        $routes->post('products/delete/(:num)', 'Products::delete/$1');
    }
);

// -------------------------------------------------------
// 🔹 CORS OPTIONS for ALL API Routes (preflight support)
// -------------------------------------------------------
$routes->options('api/(.*)', function () {
    return service('response')
        ->setHeader('Access-Control-Allow-Origin', '*')
        ->setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS')
        ->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With')
        ->setHeader('Access-Control-Max-Age', '3600')
        ->setStatusCode(200);
});

// -------------------------------------------------------
// 🔹 API Routes (for Flutter)
// -------------------------------------------------------
$routes->group('api', function ($routes) {

    // AUTH APIs
    $routes->post('auth/register', 'Api\Auth::register');
    $routes->post('auth/login', 'Api\Auth::login');
    $routes->get('auth/users', 'Api\Auth::users');

    // PRODUCTS APIs
    $routes->get('products', 'Api\Products::index');
    $routes->get('products/(:num)', 'Api\Products::show/$1');
    $routes->post('products', 'Api\Products::create');
    $routes->put('products/(:num)', 'Api\Products::update/$1');
    $routes->delete('products/(:num)', 'Api\Products::delete/$1');

    // CATEGORIES APIs
    $routes->get('categories', 'Api\Categories::index');
    $routes->get('categories/(:num)', 'Api\Categories::show/$1');
    $routes->post('categories', 'Api\Categories::create');
    $routes->put('categories/(:num)', 'Api\Categories::update/$1');
    $routes->delete('categories/(:num)', 'Api\Categories::delete/$1');

    // ---------------------------------------------------
    // 🔹 ORDERS APIs (FIXED, FULLY WORKING)
    // ---------------------------------------------------
    $routes->get('orders', 'Api\Orders::index');          // Admin/full list
    $routes->get('orders/me', 'Api\Orders::myOrders');    // user-specific
    $routes->post('orders', 'Api\Orders::create');        // create new
    $routes->get('orders/(:num)', 'Api\Orders::show/$1'); // view single
  


    // File Upload
    $routes->post('upload', 'Api\Upload::uploadFile');
});

// -------------------------------------------------------
// 🔹 Friendly 404
// -------------------------------------------------------
$routes->set404Override(function () {
    echo "<h1>404 - Page Not Found</h1><p>The page or API route you’re trying to access doesn’t exist.</p>";
});

// -------------------------------------------------------
// 🔹 Disable Auto Routing
// -------------------------------------------------------
$routes->setAutoRoute(false);

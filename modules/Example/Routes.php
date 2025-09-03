<?php

$routes->group('', ['namespace' => 'Modules\\Example\\Presentation\\Controllers'], function ($routes) {
    // User routes
    $routes->group('users', ['filter' => 'tenant-isolation'], function ($routes) {
        $routes->get('/', 'UserController::index');
        $routes->get('create', 'UserController::create');
        $routes->post('store', 'UserController::store');
        $routes->get('show/(:num)', 'UserController::show/$1');
        $routes->get('edit/(:num)', 'UserController::edit/$1');
        $routes->post('update/(:num)', 'UserController::update/$1');
        $routes->get('delete/(:num)', 'UserController::delete/$1');
        $routes->get('activate/(:num)', 'UserController::activate/$1');
        $routes->get('deactivate/(:num)', 'UserController::deactivate/$1');
    });
});

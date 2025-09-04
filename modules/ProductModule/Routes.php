<?php

use CodeIgniter\Router\RouteCollection;

$routes->group('productmodule', function ($routes) {
    $routes->get('/', 'ProductModule\Presentation\Controllers\ProductModuleController::index');
    $routes->get('create', 'ProductModule\Presentation\Controllers\ProductModuleController::create');
    $routes->post('store', 'ProductModule\Presentation\Controllers\ProductModuleController::store');
    $routes->get('edit/(:num)', 'ProductModule\Presentation\Controllers\ProductModuleController::edit/$1');
    $routes->post('update/(:num)', 'ProductModule\Presentation\Controllers\ProductModuleController::update/$1');
    $routes->get('delete/(:num)', 'ProductModule\Presentation\Controllers\ProductModuleController::delete/$1');
});
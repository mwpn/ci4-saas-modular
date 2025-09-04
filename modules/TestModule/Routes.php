<?php

use CodeIgniter\Router\RouteCollection;

$routes->group('testmodule', function ($routes) {
    $routes->get('/', 'TestModule\Presentation\Controllers\TestModuleController::index');
    $routes->get('create', 'TestModule\Presentation\Controllers\TestModuleController::create');
    $routes->post('store', 'TestModule\Presentation\Controllers\TestModuleController::store');
    $routes->get('edit/(:num)', 'TestModule\Presentation\Controllers\TestModuleController::edit/$1');
    $routes->post('update/(:num)', 'TestModule\Presentation\Controllers\TestModuleController::update/$1');
    $routes->get('delete/(:num)', 'TestModule\Presentation\Controllers\TestModuleController::delete/$1');
});
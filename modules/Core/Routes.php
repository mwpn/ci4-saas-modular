<?php

$routes->group('', ['namespace' => 'Modules\\Core\\Presentation\\Controllers'], function ($routes) {
    $routes->get('/', 'HomeController::index');

    // Tenant routes
    $routes->group('tenants', ['filter' => 'tenant-isolation'], function ($routes) {
        $routes->get('/', 'TenantController::index');
        $routes->get('create', 'TenantController::create');
        $routes->post('store', 'TenantController::store');
        $routes->get('show/(:num)', 'TenantController::show/$1');
        $routes->get('edit/(:num)', 'TenantController::edit/$1');
        $routes->post('update/(:num)', 'TenantController::update/$1');
        $routes->get('delete/(:num)', 'TenantController::delete/$1');
        $routes->get('activate/(:num)', 'TenantController::activate/$1');
        $routes->get('deactivate/(:num)', 'TenantController::deactivate/$1');
        $routes->get('suspend/(:num)', 'TenantController::suspend/$1');
    });
});

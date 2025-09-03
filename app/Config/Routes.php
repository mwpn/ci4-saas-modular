<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// Onboarding routes
$routes->group('onboarding', function ($routes) {
    $routes->get('choose-tenant', 'OnboardingController::chooseTenant');
    $routes->post('set-tenant', 'OnboardingController::setTenant');
    $routes->get('switch-tenant', 'OnboardingController::switchTenant');
});

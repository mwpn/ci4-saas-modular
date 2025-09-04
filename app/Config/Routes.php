<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// Public routes (landing page)
$routes->get('/', 'Home::index');
$routes->get('home', 'Home::index');

// Dashboard route (protected)
$routes->get('dashboard', 'DashboardController::index', ['filter' => 'auth']);
$routes->get('login', 'LoginController::index');
$routes->post('login', 'Modules\Auth\Presentation\Controllers\AuthController::attemptLogin');
$routes->get('register', 'RegisterController::index');
$routes->post('register', 'RegisterController::attemptRegister');

// API Routes
$routes->group('api', function ($routes) {
    $routes->get('health', 'ApiController::health');
    $routes->post('auth/login', 'ApiController::login');
    $routes->post('auth/register', 'ApiController::register');
    $routes->get('users', 'ApiController::users', ['filter' => 'auth']);
    $routes->get('dashboard/stats', 'ApiController::dashboardStats', ['filter' => 'auth']);
    $routes->get('tenants', 'ApiController::tenants', ['filter' => 'auth']);
    $routes->get('tenants/(:num)', 'ApiController::getTenant/$1', ['filter' => 'auth']);
});

// API Documentation
$routes->get('api-docs', function () {
    return redirect()->to('swagger-ui/index.html');
});

$routes->get('api/docs', function () {
    return redirect()->to('swagger-ui/index.html');
});

$routes->get('swagger-ui', function () {
    return redirect()->to('swagger-ui/index.html');
});

// Onboarding routes
$routes->group('onboarding', function ($routes) {
    $routes->get('choose-tenant', 'OnboardingController::chooseTenant');
    $routes->post('set-tenant', 'OnboardingController::setTenant');
    $routes->get('switch-tenant', 'OnboardingController::switchTenant');
});

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
$routes->get('register', 'Modules\Auth\Presentation\Controllers\AuthController::register');
$routes->post('register', 'Modules\Auth\Presentation\Controllers\AuthController::attemptRegister');

// API Routes
$routes->group('api', function ($routes) {
    $routes->get('health', 'ApiController::health');
    $routes->post('auth/login', 'ApiController::login');
    $routes->get('users', 'ApiController::users', ['filter' => 'auth']);
    $routes->get('dashboard/stats', 'ApiController::dashboardStats', ['filter' => 'auth']);
});

// API Documentation
$routes->get('api-docs', function () {
    return redirect()->to('swagger-ui/index.html');
});

// Onboarding routes
$routes->group('onboarding', function ($routes) {
    $routes->get('choose-tenant', 'OnboardingController::chooseTenant');
    $routes->post('set-tenant', 'OnboardingController::setTenant');
    $routes->get('switch-tenant', 'OnboardingController::switchTenant');
});

<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Auth routes (no tenant isolation needed)
$routes->group('auth', function ($routes) {
    $routes->get('login', 'Modules\Auth\Presentation\Controllers\AuthController::login');
    $routes->post('login', 'Modules\Auth\Presentation\Controllers\AuthController::attemptLogin');
    $routes->get('register', 'Modules\Auth\Presentation\Controllers\AuthController::register');
    $routes->post('register', 'Modules\Auth\Presentation\Controllers\AuthController::attemptRegister');
    $routes->get('logout', 'Modules\Auth\Presentation\Controllers\AuthController::logout');
    $routes->get('forgot-password', 'Modules\Auth\Presentation\Controllers\AuthController::forgotPassword');
    $routes->post('forgot-password', 'Modules\Auth\Presentation\Controllers\AuthController::attemptForgotPassword');
    $routes->get('reset-password/(:segment)', 'Modules\Auth\Presentation\Controllers\AuthController::resetPassword/$1');
    $routes->post('reset-password', 'Modules\Auth\Presentation\Controllers\AuthController::attemptResetPassword');
});

// Protected auth routes (require authentication)
$routes->group('auth', ['filter' => 'auth'], function ($routes) {
    $routes->get('profile', 'Modules\Auth\Presentation\Controllers\AuthController::profile');
    $routes->post('profile', 'Modules\Auth\Presentation\Controllers\AuthController::updateProfile');
    $routes->post('change-password', 'Modules\Auth\Presentation\Controllers\AuthController::changePassword');
});

// Public routes with aliases
$routes->get('login', 'Modules\Auth\Presentation\Controllers\AuthController::login');
$routes->get('register', 'Modules\Auth\Presentation\Controllers\AuthController::register');
$routes->get('logout', 'Modules\Auth\Presentation\Controllers\AuthController::logout');
$routes->get('forgot-password', 'Modules\Auth\Presentation\Controllers\AuthController::forgotPassword');

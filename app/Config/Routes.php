<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// Auth Page Routes
$routes->get('auth/login', static function() {
    return view('auth/login');
});

$routes->get('auth/register', static function() {
    return view('auth/register');
});

// API Routes
$routes->group('api', ['namespace' => 'App\Controllers\Api'], function($routes) {
    
    // Auth Routes
    $routes->post('auth/register', 'AuthController::register');
    $routes->post('auth/login', 'AuthController::login');
    $routes->post('auth/logout', 'AuthController::logout', ['filter' => 'auth']);
    $routes->get('auth/profile', 'AuthController::profile', ['filter' => 'auth']);
    
    // Vital Routes (Protected)
    $routes->group('vitals', ['filter' => 'auth'], function($routes) {
        $routes->get('categories', 'VitalController::getCategories');
        $routes->get('stats', 'VitalController::getStats');
        
        $routes->get('logs', 'VitalController::getLogs');
        $routes->post('logs', 'VitalController::addLog');
        $routes->get('logs/(:num)', 'VitalController::getLog/$1');
        $routes->put('logs/(:num)', 'VitalController::updateLog/$1');
        $routes->delete('logs/(:num)', 'VitalController::deleteLog/$1');
    });
});
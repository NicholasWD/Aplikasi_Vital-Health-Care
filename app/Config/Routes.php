<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// Test Routes (Debug)
$routes->get('test/db', 'Test::dbTest');
$routes->get('test/users', 'Test::userTest');
$routes->get('test/login', 'Test::loginTest');

// API Routes
$routes->group('api', ['namespace' => 'App\Controllers\Api'], function($routes) {
    
    // Auth Routes
    $routes->options('auth/register', function() { return ''; }); // Preflight
    $routes->options('auth/login', function() { return ''; }); // Preflight
    $routes->options('auth/logout', function() { return ''; }); // Preflight
    $routes->options('auth/profile', function() { return ''; }); // Preflight
    
    $routes->post('auth/register', 'AuthController::register');
    $routes->post('auth/login', 'AuthController::login');
    $routes->post('auth/logout', 'AuthController::logout', ['filter' => 'auth']);
    $routes->get('auth/profile', 'AuthController::profile', ['filter' => 'auth']);
    
    // Vital Category Routes (Protected)
    $routes->group('vital-categories', ['filter' => 'auth'], function($routes) {
        $routes->options('/', function() { return ''; }); // Preflight
        $routes->options('(:num)', function() { return ''; }); // Preflight
        
        $routes->get('/', 'VitalCategoryController::index');
        $routes->get('(:num)', 'VitalCategoryController::show/$1');
        $routes->post('/', 'VitalCategoryController::create');
        $routes->put('(:num)', 'VitalCategoryController::update/$1');
        $routes->delete('(:num)', 'VitalCategoryController::delete/$1');
    });
    
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
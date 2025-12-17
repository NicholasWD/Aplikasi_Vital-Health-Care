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
$routes->get('test/categories', 'Test::categoriesTest');
$routes->get('test/vital-categories', 'Api\VitalCategoryController::index'); // Test tanpa auth

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
    $routes->options('vital-categories', function() { return ''; }); // Preflight
    $routes->options('vital-categories/(:num)', function() { return ''; }); // Preflight
    
    $routes->get('vital-categories', 'VitalCategoryController::index', ['filter' => 'auth']);
    $routes->get('vital-categories/(:num)', 'VitalCategoryController::show/$1', ['filter' => 'auth']);
    $routes->post('vital-categories', 'VitalCategoryController::create', ['filter' => 'auth']);
    $routes->put('vital-categories/(:num)', 'VitalCategoryController::update/$1', ['filter' => 'auth']);
    $routes->delete('vital-categories/(:num)', 'VitalCategoryController::delete/$1', ['filter' => 'auth']);
    
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
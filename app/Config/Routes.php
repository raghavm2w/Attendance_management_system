<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/login', 'AuthController::index');
$routes->post('/login', 'AuthController::login', ['filter' => 'csrf']);
$routes->post('/logout', 'AuthController::logout', ['filter' => 'csrf']);

$routes->group('admin', ['filter' => ['auth', 'admin']], function ($routes) {
    $routes->get('dash', 'Admin\DashboardController::index');
    $routes->get('users', 'Admin\UserController::users');
    $routes->post('add-user', 'Admin\UserController::createUser', ['filter' => 'csrf']);
    $routes->get('shifts', 'Admin\ShiftController::index');
    $routes->get('attendance', 'Admin\AttendanceController::index');
    $routes->get('leaves', 'Admin\LeaveController::index');
    $routes->get('reports', 'Admin\ReportController::index');
});

$routes->group('api',['filter'=>'auth'],function($routes){
    $routes->get('home', 'UserController::index');
});



<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/login', 'AuthController::index');
$routes->get('/admin/dash', 'Admin/DashboardController::index');
$routes->get('/', 'UserController::index');


$routes->post('/login', 'AuthController::login');


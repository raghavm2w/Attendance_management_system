<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/login', 'AuthController::index');
$routes->post('/login', 'AuthController::login');
$routes->post('/logout', 'AuthController::logout', ['filter' => 'csrf']);

$routes->group('admin', ['filter' => ['auth', 'admin']], function ($routes) {
    $routes->get('dash', 'Admin\DashboardController::index');
    $routes->get('users', 'Admin\UserController::users');
    $routes->get('fetch-users', 'Admin\UserController::fetchUsers');
    $routes->post('add-user', 'Admin\UserController::createUser', ['filter' => 'csrf']);
    $routes->post('update-user/(:num)', 'Admin\UserController::updateUser/$1', ['filter' => 'csrf']);
    $routes->post('delete-user/(:num)', 'Admin\UserController::deleteUser/$1', ['filter' => 'csrf']);
    $routes->post('restore-user/(:num)', 'Admin\UserController::restoreUser/$1', ['filter' => 'csrf']);
    $routes->get('shifts', 'Admin\ShiftController::index');
    $routes->get('fetch-shifts', 'Admin\ShiftController::fetchShifts');
    $routes->post('add-shift', 'Admin\ShiftController::createShift', ['filter' => 'csrf']);
    $routes->post('update-shift/(:num)', 'Admin\ShiftController::updateShift/$1', ['filter' => 'csrf']);
    $routes->post('delete-shift/(:num)', 'Admin\ShiftController::deleteShift/$1', ['filter' => 'csrf']);
    $routes->get('user-shifts', 'Admin\ShiftController::userShifts');
    $routes->post('assign-shift', 'Admin\ShiftController::assignShift', ['filter' => 'csrf']);
    $routes->post('bulk-assign-shift', 'Admin\ShiftController::bulkAssignShift', ['filter' => 'csrf']);
    $routes->get('attendance', 'Admin\AttendanceController::index');
    $routes->get('leaves', 'Admin\LeaveController::index');
    $routes->get('reports', 'Admin\ReportController::index');
    $routes->post('import-users','Admin\UserController::importUsers',['filter' => 'csrf']);
    $routes->get('export-users','Admin\UserController::exportUsers');
    $routes->get('settings', 'Admin\SettingsController::index');
    $routes->get('settings/ips', 'Admin\SettingsController::ips');
    $routes->get('settings/fetch-ips', 'Admin\SettingsController::fetchIps');
    $routes->post('settings/add-ip', 'Admin\SettingsController::createIp', ['filter' => 'csrf']);
    $routes->post('settings/update-ip/(:num)', 'Admin\SettingsController::updateIp/$1', ['filter' => 'csrf']);
    $routes->post('settings/delete-ip/(:num)', 'Admin\SettingsController::deleteIp/$1', ['filter' => 'csrf']);
    $routes->post('settings/restore-ip/(:num)', 'Admin\SettingsController::restoreIp/$1', ['filter' => 'csrf']);
    $routes->get('settings/timezone', 'Admin\SettingsController::timezone');
    $routes->post('settings/update-timezone', 'Admin\SettingsController::updateTimezone', ['filter' => 'csrf']);
    $routes->get('policy/holiday', 'Admin\PolicyController::holiday');
    $routes->post('policy/holiday/update-weekly-off', 'Admin\PolicyController::updateWeeklyOff', ['filter' => 'csrf']);
    $routes->post('policy/holiday/save', 'Admin\PolicyController::saveHoliday', ['filter' => 'csrf']);
    $routes->post('policy/holiday/delete/(:num)', 'Admin\PolicyController::deleteHoliday/$1', ['filter' => 'csrf']);
    $routes->get('policy/leave', 'Admin\PolicyController::leaves');
    $routes->post('policy/leave', 'Admin\PolicyController::storeLeaveType', ['filter' => 'csrf']);
    $routes->post('policy/leave/update/(:num)', 'Admin\PolicyController::updateLeaveType/$1', ['filter' => 'csrf']);
    $routes->post('policy/leave/delete/(:num)', 'Admin\PolicyController::deleteLeaveType/$1', ['filter' => 'csrf']);

});

$routes->group('user', ['filter' => 'auth'], function ($routes) {
    $routes->get('home', 'User\HomeController::index');
    $routes->get('attendance', 'User\AttendanceController::index');
    $routes->get('leaves', 'User\LeaveController::index');
    $routes->post('check-in','User\HomeController::checkIn');
});



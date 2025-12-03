<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->get('users', 'UsersController::index');
$routes->post('user', 'UsersController::insert');

$routes->post('auth/login', 'UsersController::login');

$routes->post('laporin', 'LaporinController::insert');

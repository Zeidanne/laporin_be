<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->get('users', 'UsersController::index');
$routes->post('user/create', 'UsersController::create');

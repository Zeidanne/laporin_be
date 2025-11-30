<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->get('users', 'UsersController::index');
$routes->get('user/create', 'UsersController::create');

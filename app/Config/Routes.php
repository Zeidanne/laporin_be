<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->get('users', 'UsersController::index');

$routes->post('auth/register', 'UsersController::insert');
$routes->post('auth/login', 'UsersController::login');

$routes->get('profile/(:num)', 'UsersController::getProfile/$1');
$routes->put('profile/(:num)', 'UsersController::updateProfile/$1');

$routes->post('laporin', 'LaporinController::insert');
$routes->get('laporin/jenis', 'LaporinController::jenisLaporan');

$routes->post('media/upload', 'MediaController::uploadSingle');
$routes->delete('media/delete', 'MediaController::deleteSingle');

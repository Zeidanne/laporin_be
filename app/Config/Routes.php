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
$routes->get('laporin/riwayat/(:num)', 'LaporinController::riwayatUser/$1');
$routes->get('laporin/detail/(:num)', 'LaporinController::detailUser/$1');

$routes->get('penindak/laporin', 'PenindakLaporinController::index');
$routes->get('penindak/laporin/detail/(:num)', 'PenindakLaporinController::detail/$1');

$routes->post('media/upload', 'MediaController::uploadSingle');
$routes->delete('media/delete', 'MediaController::deleteSingle');

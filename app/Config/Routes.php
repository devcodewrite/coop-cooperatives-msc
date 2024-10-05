<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->post('sync/pull', 'SyncController::pull');
$routes->post('sync/push', 'SyncController::push');

<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->post('sync/pullChanges', 'SyncController::pullChanges');
$routes->post('sync/pushChanges', 'SyncController::pushChanges');

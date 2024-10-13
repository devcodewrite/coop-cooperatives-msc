<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->get('regions/sync-pull', "RegionController::pull");
$routes->post('regions/sync-push', "RegionController::push");
$routes->get('districts/sync-pull', "DistrictController::pull");
$routes->post('districts/sync-push', "DistrictController::push");
$routes->get('offices/sync-pull', "OfficeController::pull");
$routes->post('offices/sync-push', "OfficeController::push");

$routes->resource('districts', ['controller' => 'DistrictController']);
$routes->resource('regions', ['controller' => 'RegionController']);
$routes->resource('organizations', ['controller' => 'OrganizationController']);
$routes->resource('offices', ['controller' => 'OfficeController']);
$routes->resource('communities', ['controller' => 'CommunityController']);
$routes->resource('associations', ['controller' => 'AssociationController']);
$routes->resource('accounts', ['controller' => 'AccountController']);
$routes->resource('passbooks', ['controller' => 'PassbookController']);

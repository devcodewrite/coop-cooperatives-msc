<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->get('regions/sync-pull', "RegionController::pull");

$routes->resource('districts', ['controller' => 'DistrictController']);
$routes->resource('regions', ['controller' => 'RegionController']);
$routes->resource('organizations', ['controller' => 'OrganizationController']);
$routes->resource('offices', ['controller' => 'OfficeController']);
$routes->resource('communities', ['controller' => 'CommunityController']);
$routes->resource('associations', ['controller' => 'AssociationController']);
$routes->resource('accounts', ['controller' => 'AccountController']);
$routes->resource('passbooks', ['controller' => 'PassbookController']);

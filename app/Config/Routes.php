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
$routes->get('communities/sync-pull', "CommunityController::pull");
$routes->post('communities/sync-push', "CommunityController::push");
$routes->get('associations/sync-pull', "AssociationController::pull");
$routes->post('associations/sync-push', "AssociationController::push");
$routes->get('passbooks/sync-pull', "PassbookController::pull");
$routes->post('passbooks/sync-push', "PassbookController::push");
$routes->get('accounts/sync-pull', "AccountController::pull");
$routes->post('accounts/sync-push', "AccountController::push");

$routes->resource('districts', ['only' => 'index,view', 'controller' => 'DistrictController']);
$routes->resource('regions', ['only' => 'index,view', 'controller' => 'RegionController']);
$routes->resource('organizations', ['controller' => 'OrganizationController']);
$routes->resource('offices', ['controller' => 'OfficeController']);
$routes->resource('communities', ['controller' => 'CommunityController']);
$routes->resource('associations', ['controller' => 'AssociationController']);
$routes->resource('accounts', ['controller' => 'AccountController']);
$routes->resource('passbooks', ['controller' => 'PassbookController']);

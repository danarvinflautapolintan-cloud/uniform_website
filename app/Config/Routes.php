<?php
use CodeIgniter\Router\RouteCollection;
/** @var RouteCollection $routes */
$routes->group('', ['filter' => 'csrf'], static function ($routes) {
    $routes->get('/', 'Shop::index');
    $routes->match(['get', 'post'], 'login', 'Shop::login');
    $routes->match(['get', 'post'], 'register', 'Shop::register');
    $routes->post('logout', 'Shop::logout');
    $routes->post('reserve', 'Shop::reserve');
    $routes->post('cancel', 'Shop::cancel');
    $routes->get('mine', 'Shop::mine');

    $routes->get('market', 'Market::index');
    $routes->get('market/sell', 'Market::sell');
    $routes->get('market/my', 'Market::my');
    $routes->post('market/save', 'Market::save');
    $routes->post('market/delete', 'Market::delete');
    $routes->post('market/buy', 'Market::buy');
    $routes->post('market/cancel', 'Market::cancel');
    $routes->post('market/decide', 'Market::decide');
    $routes->post('market/sold', 'Market::sold');
    $routes->post('market/release', 'Market::release');

    $routes->group('admin', static function ($routes) {
        $routes->match(['get', 'post'], 'login', 'Admin::login');
        $routes->post('logout', 'Admin::logout');
        $routes->get('/', 'Admin::index');
        $routes->get('users', 'Admin::users');
        $routes->get('uniforms/new', 'Admin::form');
        $routes->get('uniforms/(:num)/edit', 'Admin::form/$1');
        $routes->post('uniforms/save', 'Admin::save');
        $routes->post('uniforms/delete', 'Admin::delete');
        $routes->get('requests', 'Admin::requests');
        $routes->post('requests/act', 'Admin::act');
        $routes->post('requests/delete', 'Admin::deleteRequest');
        $routes->post('requests/clear', 'Admin::clearRequests');
        $routes->get('report', 'Admin::report');
        $routes->get('listings', 'AdminListings::index');
        $routes->post('listings/act', 'AdminListings::act');
        $routes->post('listings/delete', 'AdminListings::delete');
    });
});

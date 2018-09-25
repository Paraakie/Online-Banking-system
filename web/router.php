<?php
use PHPRouter\RouteCollection;
use PHPRouter\Router;
use PHPRouter\Route;

$collection = new RouteCollection();

// example of using a redirect to another route
$collection->attachRoute(
    new Route(
        '/',
        array(
            '_controller' => 'agilman\a2\controller\HomeController::createLoginPage',
            'methods' => 'GET',
            'name' => 'Home'
        )
    )
);

$collection->attachRoute(
    new Route(
        '/account/',
        array(
        '_controller' => 'agilman\a2\controller\AccountController::indexAction',
        'methods' => 'GET',
        'name' => 'accountIndex'
        )
    )
);

$collection->attachRoute(
    new Route(
        '/account/create/',
        array(
        '_controller' => 'agilman\a2\controller\AccountController::createAction',
        'methods' => 'GET',
        'name' => 'accountCreate'
        )
    )
);

$collection->attachRoute(
    new Route(
        '/account/delete/:id',
        array(
        '_controller' => 'agilman\a2\controller\AccountController::deleteAction',
        'methods' => 'GET',
        'name' => 'accountDelete'
        )
    )
);

$collection->attachRoute(
    new Route(
        '/account/update/:id',
        array(
        '_controller' => 'agilman\a2\controller\AccountController::updateAction',
        'methods' => 'GET',
        'name' => 'accountUpdate'
        )
    )
);
/**
 * our login router
 */
$collection->attachRoute(
    new Route(
        '/login/',
        array(
            '_controller' => 'agilman\a2\controller\HomeController::createLoginPage',
            'methods' => 'GET',
            'name' => 'login'
        )
    )
);

/**
 * our validateLogin router
 */

$collection->attachRoute(
    new Route(
        '/',
        array(
            '_controller' => 'agilman\a2\controller\UserAccountController::validateLogin',
            'methods' => 'POST',
            'name' => 'validateLogin'
        )
    )
);

$collection->attachRoute(
    new Route(
        '/',
        array(
            '_controller' => 'agilman\a2\controller\HomeController::createSignUpPage',
            'methods' => 'POST',
            'name' => 'signUp'
        )
    )
);
$router = new Router($collection);
$router->setBasePath('/');

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
            '_controller' => 'agilman\a2\controller\HomeController::indexAction',
            'methods' => 'GET',
            'name' => 'Home'
        )
    )
);

$collection->attachRoute(
    new Route(
        '/account/',
        array(
        '_controller' => 'agilman\a2\controller\AccountController::showAccounts',
        'methods' => 'GET',
        'name' => 'showAccounts'
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
            '_controller' => 'agilman\a2\controller\UserAccountController::login',
            'methods' => 'GET',
            'name' => 'login'
        )
    )
);

$collection->attachRoute(
    new Route(
        '/login/',
        array(
            '_controller' => 'agilman\a2\controller\UserAccountController::login',
            'methods' => 'POST',
            'name' => 'login'
        )
    )
);

$collection->attachRoute(
    new Route(
        '/signUp/',
        array(
            '_controller' => 'agilman\a2\controller\UserAccountController::signUp',
            'methods' => 'POST',
            'name' => 'signUp'
        )
    )
);

$collection->attachRoute(
    new Route(
        '/signUp/',
        array(
            '_controller' => 'agilman\a2\controller\UserAccountController::signUp',
            'methods' => 'GET',
            'name' => 'signUp'
        )
    )
);

/**
 * Transaction Pages
 */
$collection->attachRoute(
    new Route(
        '/transIndex/',
        array(
            '_controller' => 'agilman\a2\controller\TransController::createTransIndexPage',
            'methods' => 'GET',
            'name' => 'createTransIndexPage'
        )
    )
);

/**
 * Logout router
 */
$collection->attachRoute(
    new Route(
        '/account/logout',
        array(
            '_controller' => 'agilman\a2\controller\HomeController::logout',
            'methods' => 'GET',
            'name' => 'logout'
        )
    )
);



$router = new Router($collection);
$router->setBasePath('/');

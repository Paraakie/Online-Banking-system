<?php
/**
 * 159.339 Internet Programming 2017.2
 * Student ID: 99999999
 * Assignment: 2   Date: 01/09/17
 * System: PHP 7.1
 * Code guidelines: PSR-1, PSR-2
 *
 * FRONT CONTROLLER - Responsible for URL routing and User Authentication
 *
 * @package agilman/a2
 * @author  A. Gilman <a.gilman@massey.ac.nz>
 **/
namespace agilman\a2;
date_default_timezone_set('Pacific/Auckland');
const APP_ROOT = __DIR__;

require 'vendor/autoload.php';
require 'router.php';

$route = $router->matchCurrentRequest();

// If route was dispatched successfully - return
if ($route) {
    $accessLogMessage = $_SERVER['REMOTE_ADDR'] . ":" .
        $_SERVER['REMOTE_PORT'] . " " .
        "Dispatched " .
        $router->generate($route->getName(), $route->getParameters()) .
        " using " . $_SERVER['REQUEST_METHOD'] . " successfully.";
    error_log($accessLogMessage, 4);
    // true indicates to webserver that the route was successfully served
    return true;
}

// Otherwise check if the request is for a static resource
$info = parse_url($_SERVER['REQUEST_URI']);
// check if its an allowed static resource type and that the file exists
if (preg_match('/\.(?:png|jpg|jpeg|css|js)$/', "$info[path]")
    && file_exists("./$info[path]")) {
    // false indicates to web server that the route is for a static file - fetch it and return to client
    return false;
} else {
    $accessLogMessage = $_SERVER['REMOTE_ADDR'] . ":" .
        $_SERVER['REMOTE_PORT'] . " " .
        "URL " . $_SERVER['REQUEST_URI'] . " didn't match a route or static file.";
    error_log($accessLogMessage, 4);
    header("HTTP/1.0 404 Not Found");
    // Custom error page
    // require 'static/html/404.html';
    return true;
}

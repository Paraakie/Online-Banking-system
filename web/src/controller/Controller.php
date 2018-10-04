<?php

namespace jis\a2\controller;

/**
 * Class Controller defines helper functions to be used by other controllers
 *
 * @package jis/a2
 * @author  Andrew Gilman <a.gilman@massey.ac.nz>
 * @author Isaac Clancy, Junyi Chen, Sven Gerhards
 */
class Controller
{
    /**
     * Redirect to another route
     *
     * @param $route
     * @param array $params
     */
    public function redirect(string $route, $params = []): void
    {
        // Generate a redirect url for a given named route
        $url = $GLOBALS['router']->generate($route, $params);
        header("Location: $url");
    }

    /**
     * This function returns the url for the name of a route
     * @param string $route Route's name
     * @param array $params
     * @return string this is the url that user will get
     */
    public static function getUrl(string $route, $params = []): string
    {
        return $GLOBALS['router']->generate($route, $params);
    }
}

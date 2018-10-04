<?php

namespace agilman\a2\controller;

/**
 * Class Controller
 *
 * @package agilman/a2
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
    public function redirect($route, $params = [])
    {
        // Generate a redirect url for a given named route
        $url = $GLOBALS['router']->generate($route, $params);
        header("Location: $url");
    }

    /**
     * This function
     * @param string $route Router's name
     * @param array $params
     * @return string  this is the url that user will get
     */
    public static function getUrl(string $route, $params = []): string
    {
        return $GLOBALS['router']->generate($route, $params);
    }
}

<?php

namespace jis\a2\controller;

/**
 * Class HomeController handles redirecting to the right page when the default page is requested
 *
 * @package jis/a2
 * @author  Andrew Gilman <a.gilman@massey.ac.nz>
 * @author Isaac Clancy, Junyi Chen, Sven Gerhards
 */
class HomeController extends Controller
{
    /**
     * Account Index action
     */
    public function indexAction()
    {
        //session checking
        session_start();
        if (isset($_SESSION['userID'])) {
            //user is logged in, redirect to the home page
            $this->redirect('showAccounts');
        } else {
            //user hasn't logged in, to login page
            $this->redirect('login');
        }
    }

    /**
     *  Logout Action
     */
    public function logout()
    {
        //Deleting session
        session_start();
        session_destroy();
        $this->redirect('login');
    }
}

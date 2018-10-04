<?php
namespace agilman\a2\controller;

use agilman\a2\view\View;
/**
 * Class HomeController
 *
 * @package agilman/a2
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
        if(isset($_SESSION['userID'])){
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
    public function logout(){
        //Deleteing session
        session_start();
        session_destroy();
        $this->redirect('login');
    }

}

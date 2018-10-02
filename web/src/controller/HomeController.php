<?php
namespace agilman\a2\controller;

use agilman\a2\view\View;
/**
 * Class HomeController
 *
 * @package agilman/a2
 * @author  Andrew Gilman <a.gilman@massey.ac.nz>
 */
class HomeController extends Controller
{
    /**
     * Account Index action
     */
    public function indexAction()
    {
        session_start();
        if(isset($_SESSION['userID'])){
            $this->redirect('showAccounts');
        } else {
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

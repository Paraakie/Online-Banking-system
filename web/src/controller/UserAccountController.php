<?php
/**
 * Created by PhpStorm.
 * User: Isaac
 * Date: 25/09/2018
 * Time: 2:43 PM
 */

namespace agilman\a2\controller;

use agilman\a2\view\View;
use agilman\a2\model\UserAccountModel;

class UserAccountController extends Controller
{
    private function validateLogin()
    {
        $name = $_POST["userName"];
        $password = $_POST["userPassword"];
        $user = (new UserAccountModel())->loadByNameAndPassword($name, $password);
        if($user !== null) {
            $this->redirect('accountIndex');
        } else {
            $view = new View('login');
            echo $view->addData("error", "Invalid user name or password")->render();

        }
    }

    public function createSignUpPage(){
        $view = new View('signUp');
        echo $view->render();
    }

    public function login(){
        if (isset($_POST['validateLogin'])) {
            $this->validateLogin();
        }
        elseif (isset($_POST['signUp'])) {
            $this->redirect('signUp');
        } else {
            $view = new View('login');
            echo $view->render();
        }
    }
}
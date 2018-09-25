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
    public function validateLogin()
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
}
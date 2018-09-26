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
    private const MIN_PASSWORD_LENGTH = 4;

    private function validateLogin()
    {
        $name = $_POST["userName"];
        $password = $_POST["userPassword"];
        $user = (new UserAccountModel())->loadByNameAndPassword($name, $password);
        if($user !== null) {
            session_start();
            $_SESSION['userName'] = $name;
            $_SESSION['userID'] = $user->getId();
            $this->redirect('showAccounts');
        } else {
            $view = new View('login');
            echo $view->addData("error", "Invalid user name or password")->render();

        }
    }

    public function signUp(){
        if(isset($_POST['signUp'])) {
            $name = $_POST["userName"];
            $password = $_POST["userPassword"];
            $password2 = $_POST["userPassword2"];
            if($password !== $password2) {
                $view = new View('signUp');
                echo $view->addData("error", "The two passwords must match")->render();
                return;
            }
            if(strlen($password) < UserAccountController::MIN_PASSWORD_LENGTH) {
                $view = new View('signUp');
                echo $view->addData("error", 'Your password must be at least '
                    . UserAccountController::MIN_PASSWORD_LENGTH . ' characters long')
                    ->render();
                return;
            }
            $userAccount = new UserAccountModel();
            if($userAccount->loadByName($name) != null) {
                $view = new View('signUp');
                echo $view->addData("error", "The account name is already in use")->render();
                return;
            }

            $userAccount->setName($name);
            $userAccount->setPassword($password);
            $userAccount->save();
            session_start();
            $_SESSION['userName'] = $name;
            $_SESSION['userID'] = $userAccount->getId();
            $this->redirect("showAccounts");
        } else {
            $view = new View('signUp');
            echo $view->render();
        }
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
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

    /**
     * Creates a new User Account if the user input is valid, otherwise returns an error
     * @param string $password The password for the new account
     * @param string $password2 A repeat of the password, will be checked against $password
     * @param string $name The name of the account
     * @return null|string If an error occurred the error message, otherwise null
     */
    private function handleSignUp(string $password, string $password2, string $name): ?string
    {
        if($password !== $password2) {
            return 'The two passwords must match';
        }
        if(strlen($password) < UserAccountController::MIN_PASSWORD_LENGTH) {
            return 'Your password must be at least '
                . UserAccountController::MIN_PASSWORD_LENGTH . ' characters long';
        }
        $userAccount = new UserAccountModel();
        if($userAccount->loadByName($name) != null) {
            return 'The account name is already in use';
        }

        $userAccount->setName($name);
        $userAccount->setPassword($password);
        $userAccount->save();
        session_start();
        $_SESSION['userName'] = $name;
        $_SESSION['userID'] = $userAccount->getId();

        return null;
    }

    /**
     * handles the login for letting a user create an account
     */
    public function signUp(){
        if(isset($_POST['signUp'])) {
            $name = $_POST["userName"];
            $password = $_POST["userPassword"];
            $password2 = $_POST["userPassword2"];
            $error = $this->handleSignUp($password, $password2, $name);
            if($error === null) {
                $this->redirect("showAccounts");
            } else {
                $view = new View('signUp');
                echo $view->addData("error", $error)->render();
            }
        } else {
            $view = new View('signUp');
            echo $view->render();
        }
    }

    /**
     * Logs in if the userName and userPassword in $_SESSION match a user account
     * @return null|string The error message if an error occurred, otherwise null
     */
    private function handleLogin(): ?string
    {
        $user = (new UserAccountModel())->loadByNameAndPassword($_SESSION['userName'], $_SESSION['userPassword']);
        if($user !== null) {
            $_SESSION['userID'] = $user->getId();
            return null;
        } else {
            return 'Invalid user name or password';
        }
    }

    /**
     * Handles the logic for the login page
     */
    public function login(){
        session_start();
        $_SESSION['userName'] = $_POST["userName"];
        $_SESSION['userPassword'] = $_POST["userPassword"];
        if (isset($_POST['validateLogin'])) {
            $error = $this->handleLogin();
            if($error === null) {
                $this->redirect('showAccounts');
            } else {
                $view = new View('login');
                $view->addData('userName', $_SESSION['userName']);
                $view->addData('userPassword', $_SESSION['userPassword']);
                echo $view->addData("error", $error)->render();
            }
        }
        elseif (isset($_POST['signUp'])) {
            $this->redirect('signUp');
        } else {
            $view = new View('login');
            echo $view->render();
        }
    }

    /**
     * Gets the current user if their is one or redirects to login
     * @return UserAccountModel|null The current user if their is one, otherwise null
     */
    public static function getCurrentUser(): ?UserAccountModel
    {
        session_start();
        if(isset($_SESSION['userID'])) {
            $userId = $_SESSION['userID'];
            return (new UserAccountModel())->loadByID($userId);
        } else {
            $url = static::getUrl("login");
            header('Refresh: 3; URL='.$url);
            echo "<p align=center style=color:red;>Please login...<br> Redirecting back to login page</p>";
            return null;
        }
    }
}
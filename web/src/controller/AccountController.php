<?php
namespace agilman\a2\controller;

use agilman\a2\model\{
    BankAccountModel, UserAccountModel
};
use agilman\a2\view\View;

/**
 * Class AccountController
 *
 * @package agilman/a2
 * @author  Andrew Gilman <a.gilman@massey.ac.nz>
 */
class AccountController extends Controller
{
    /**
     * Account Index action
     */
    public function showAccounts()
    {
        session_start();
        if(isset($_SESSION['userName'])) {
            $userId = $_SESSION['userID'];
            $user = (new UserAccountModel())->loadByID($userId);
            $accounts = $user->getBankAccounts();
            $view = new View('userHome');
            $view->addData('userName', $_SESSION['userName']);
            $view->addData('accounts', $accounts);
            echo $view->render();
        } else {
            header('Refresh: 3; URL=/');
            echo "<p align=center style=color:red;>Please login...<br> Redirecting back to login page</p>";
        }
    }
    /**
     * Account Create action
     */
    public function createAction()
    {
        $account = new BankAccountModel();
        $names = ['Bob','Mary','Jon','Peter','Grace'];
        shuffle($names);
        $account->setName($names[0]); // will come from Form data
        $account->save();
        $id = $account->getId();
        $view = new View('accountCreated');
        echo $view->addData('accountId', $id)->render();
    }

    /**
     * Account Delete action
     *
     * @param int $id Account id to be deleted
     */
    public function deleteAction($id)
    {
        $user = UserAccountController::getCurrentUser();
        if($user == null) {
            return;
        }
        $bankAccount = $user->getBankAccountByID($id);
        if($bankAccount !== null) {
            $bankAccount->delete();
            $view = new View('accountClosed');
            $view->addData('deleted', true);
            echo $view->addData('accountId', $id)->render();
        } else {
            echo "hello world";
            $view = new View('accountClosed');
            $view->addData('deleted', false);
            echo $view->addData('accountId', $id)->render();
        }
    }
    /**
     * Account Update action
     *
     * @param int $id Account id to be updated
     */
    public function updateAction($id)
    {
        $account = (new BankAccountModel())->load($id);
        $account->setName('Joe')->save(); // new name will come from Form data
    }
}

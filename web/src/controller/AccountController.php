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
        $user = UserAccountController::getCurrentUser();
        if($user !== null) {
            $accounts = $user->getBankAccounts();
            $view = new View('userHome');
            $view->addData('userName', $_SESSION['userName']);
            $view->addData('accounts', $accounts);
            echo $view->render();
        }
    }
    /**
     * Account Create action
     */
    public function createAction()
    {
        $user = UserAccountController::getCurrentUser();
        if($user === null) {
            return;
        }
        $account = new BankAccountModel();
        $names = ['Bob','Mary','Jon','Peter','Grace'];
        shuffle($names);
        $account->setName($names[0]); // will come from Form data
        $account->setBalance(0);
        $account->setUserId($user->getID());
        $account->save();
        $id = $account->getId();
        $view = new View('accountCreated');
        $view->addData('accountId', $id);
        echo $view->render();
    }

    /**
     * Account Delete action
     *
     * @param int $id Account id to be deleted
     */
    public function deleteAction($id)
    {
        /**
         * @var UserAccountModel this object is used to check user information
         */
        $user = UserAccountController::getCurrentUser();
        /** user hasn't logged in */
        if($user == null) {
            return;
        }

        /**
         * @var BankAccountModel this object is used to check current user's authority to the account
         */
        $bankAccount = $user->getBankAccountByID($id);

        /** current user is legit to modify the account*/
        if($bankAccount !== null ) {
            if ($bankAccount->getBalance() != 0) {
                /** account has money left, delete failure*/
                $view = new View('accountClosed');
                $view->addData('deleted', false);
                $view->addData('message', "You still have money left in your account!");
                echo $view->addData('accountId', $id)->render();
            } else {
                /** account deleted successfully */
                $bankAccount->delete();
                $view = new View('accountClosed');
                $view->addData('deleted', true);
                echo $view->addData('accountId', $id)->render();
            }
        } else {
            /**  current user is not legit to modify this account*/
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


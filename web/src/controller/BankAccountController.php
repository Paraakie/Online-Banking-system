<?php

namespace jis\a2\controller;

use jis\a2\model\{
    BankAccountModel, UserAccountModel
};
use jis\a2\view\View;

/**
 * Handles all requests related to the bank accounts of a user
 *
 * @package jis/a2
 * @author  Andrew Gilman <a.gilman@massey.ac.nz>
 * @author Isaac Clancy, Junyi Chen, Sven Gerhards
 */
class BankAccountController extends Controller
{
    /**
     * Shows all bank accounts for a user
     */
    public function showAccounts()
    {
        //User authenticity
        /**
         * @var UserAccountModel this object is used to check user information
         */
        $user = UserAccountController::getCurrentUser();
        if ($user === null) {
            //User is not logged in
            return;
        }

        $scripts = '
            <script type="text/javascript">
            let accounts,
                order1 = 1,
                order2 = 1,
                order3 = 1;
            window.onload = function () {
                accounts = document.getElementById("accounts");
            };
        </script>
        <script type="text/javascript" src="/static/scripts/sortTable.js"></script>
        ';
        $tableHeaderOnClickListeners = [
            'sortTable(accounts, 0, order1);order1 *= -1; order2 = 1; order3 = 1;',
            'sortTable(accounts, 1, order2);order1 = 1; order2 *= -1; order3 = 1;',
            'sortTable(accounts, 2, order3);order1 = 1; order2 = 1; order3 *= -1;'
        ];

        $accounts = $user->getBankAccounts();
        $view = new View('userHome');
        $view->addData('userName', $user->getName());
        $view->addData('accounts', $accounts);
        $view->addData('scripts', $scripts);
        $view->addData('tableHeaderOnClickListeners', $tableHeaderOnClickListeners);
        echo $view->render();
    }

    /**
     * This method handles the Account Creation
     *
     * @param UserAccountModel $user The user to create a bank account for
     * @param string $accName The name of the bank account e.g. Savings
     * @return null|string An error message if an error occurred, null otherwise
     */
    public function handleAccountCreation(UserAccountModel $user, string $accName)
    {

        if ($accName == "") {
            return "Name is empty";
        }

        /**
         * @var BankAccountModel, when user successfully created a new account, this object is created which contains
         * all information about the new account.
         */
        $account = new BankAccountModel();
        $account->setName($accName); // will come from Form data
        $account->setBalance(0);
        $account->setUserId($user->getID());
        $account->save(); //this function will insert a new row in database

        return null;
    }

    /**
     * Account Create page action
     */
    public function createAction()
    {
        //User Authenticity
        $user = UserAccountController::getCurrentUser();
        if ($user === null) {
            return;
        }

        if (isset($_GET['submit'])) {
            //User clicked submit button

            $name = $_GET['accName'];
            $error = $this->handleAccountCreation($user, $name);
            if ($error === null) {
                //Account created successfully
                $okLocation = static::getUrl("showAccounts");
                $this->creationSuccessful("Account $name was created successfully!", $okLocation);
            } else {
                //Error encountered
                $view = new View('accountCreated');
                $view->addData('error', $error);
                echo $view->render();
            }
        } else {
            //Render the account creation page
            $view = new View('accountCreated');
            echo $view->render();
        }
    }

    /**
     * This function is used to render the page after successfully created a new account
     * @param string $message Contains the successful message with new account's name
     * @param string $okLocation URL back to the user home page
     */
    private function creationSuccessful(string $message, string $okLocation)
    {
        //Render the page
        $view = new View("successMessage");
        $view->addData('message', $message);
        $view->addData('okLocation', $okLocation);
        echo $view->render();
    }

    /**
     * Deletes a bank account if it belongs to the user and has a balance of zero
     *
     * @param int $id Account id to be deleted
     */
    public function deleteAction($id)
    {
        /**
         * @var UserAccountModel this object is used to check user information
         */
        $user = UserAccountController::getCurrentUser();
        if ($user == null) {
            // user hasn't logged in
            return;
        }

        /**
         * @var BankAccountModel this object is used to check current user's authority to the account
         */
        $bankAccount = $user->getBankAccountByID($id);
        if ($bankAccount !== null) {
            //current user owns the bank account they are trying to modify
            if ($bankAccount->getBalance() != 0) {
                // account has money left, delete failure
                $view = new View('accountClosed');
                $view->addData('deleted', false);
                $view->addData('message', "You still have money left in your account!");
                echo $view->addData('accountId', $id)->render();
            } else {
                // account deleted successfully
                $bankAccount->delete();
                $view = new View('accountClosed');
                $view->addData('deleted', true);
                echo $view->addData('accountId', $id)->render();
            }
        } else {
            //current user doesn't owns this bank account
            $view = new View('accountClosed');
            $view->addData('deleted', false);
            echo $view->addData('accountId', $id)->render();
        }
    }
}

<?php
namespace agilman\a2\controller;

use agilman\a2\model\BankAccountModel;
use agilman\a2\model\TransactionModel;
use agilman\a2\model\UserAccountModel;
use agilman\a2\view\View;
//use agilman\a2\model\TransactionModel;
use agilman\a2\model\TransactionCollectionModel;

/**
 * Class TransController
 *
 * Created by PhpStorm.
 * User: Sven Gerhards
 * Date: 26/09/2018
 * Time: 2:08 PM
 */


class TransController extends Controller
{
    /**
     * Display the Web-page of /Transaction/
     */
    public function createTransIndexPage(){
        $collection = new TransactionCollectionModel();
        $transactions = $collection->getTransactions();
        $view = new View('transaction');
        echo $view->addData('transactions', $transactions)->render();
    }

    /**
     * Display the Web-page for /Transaction/Deposit/
     */
    public function createTransDepositPage(){
        $collection = new TransactionCollectionModel();
        $transactions = $collection->getTransactions();
        $view = new View('transDeposit');
        echo $view->addData('transactions', $transactions)->render();
    }

    private function handleTransfer(int $userId, int $fromAccountID): void
    {
        if(isset($_GET['submit'])) {
            $user = (new UserAccountModel())->loadByID($userId);
            $fromAccount = $user->getBankAccountByID($fromAccountID);
            if($fromAccount === null) {
                $this->redirect("login");
                return;
            }
            $toAccountStr = $_GET["toAccount"];
            $amountStr = $_GET["amount"];
            $toAccountID = filter_var($toAccountStr, FILTER_VALIDATE_INT);
            $toAccount = $toAccountID === FALSE ? null : (new BankAccountModel())->load($toAccountID);
            if($toAccount === null) {
                $view = new View('transTransfer');
                $view->addData('fromAccountID', $fromAccountID);
                $view->addData('error', "Invalid account ID to transfer to");
                echo $view->render();
                return;
            }
            $amount = intval(floatval($amountStr) * 100);
            if($amount < 0) {
                $view = new View('transTransfer');
                $view->addData('fromAccountID', $fromAccountID);
                $view->addData('error', "Cannot transfer a negative amount");
                echo $view->render();
                return;
            }
            $newFromAmount = $fromAccount->getBalance() - $amount;
            if($newFromAmount < $fromAccount->getMinimumAllowedBalance()) {
                $view = new View('transTransfer');
                $view->addData('fromAccountID', $fromAccountID);
                $view->addData('error', "Your bank account's balance is too low");
                echo $view->render();
                return;
            }
            $newToAmount = $toAccount->getBalance() + $amount;
            $toAccount->setBalance($newToAmount);
            $fromAccount->setBalance($newFromAmount);
            $toAccount->save();
            $fromAccount->save();
            $this->redirect('showAccounts');
        } else {
            $view = new View('transTransfer');
            echo $view->addData('$fromAccountID', $fromAccountID)->render();
        }
    }

    /**
     * Display the Web-page for /Transaction/Transfer/
     * @param int $fromAccountID The id off the account to transfer money from
     */
    public function createTransTransferPage(int $fromAccountID) {
        session_start();
        if(isset($_SESSION['userName'])) {
            $userId = $_SESSION['userID'];
            $this->handleTransfer($userId, $fromAccountID);
        } else {
            header('Refresh: 3; URL=/');
            echo "<p align=center style=color:red;>Please login...<br> Redirecting back to login page</p>";
        }
    }

    /**
     * Display the Web-page for /Transaction/Withdrawal/
     */
    public function createTransWithdrawalPage(){
        $collection = new TransactionCollectionModel();
        $transactions = $collection->getTransactions();
        $view = new View('transWithdrawal');
        echo $view->addData('transactions', $transactions)->render();
    }

    /**
     * Create Transaction - Unfiinished
     */
    public function createTransaction(){
        $transaction = new TransactionModel();
        $transaction->setTime();
        //...
        $transaction->save();
        $view = new View('transactionCreated');
        echo $view->addData('transaction', $transaction)->render();
    }
}

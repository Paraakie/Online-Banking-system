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
    /**
     * Display the Web-page for /Transaction/Transfer/
     */
    public function createTransTransferPage(){
        $collection = new TransactionCollectionModel();
        $transactions = $collection->getTransactions();
        $view = new View('transTransfer');
        echo $view->addData('transactions', $transactions)->render();
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

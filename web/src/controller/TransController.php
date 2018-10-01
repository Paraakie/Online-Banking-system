<?php
namespace agilman\a2\controller;

use agilman\a2\model\BankAccountModel;
use agilman\a2\model\TransactionModel;
use agilman\a2\model\UserAccountModel;
use agilman\a2\view\View;

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
        $user = UserAccountController::getCurrentUser();
        if($user === null) {
            return;
        }
        $bankAccounts = $user->getBankAccounts();
        $transactions = new \AppendIterator();
        foreach($bankAccounts as $bankAccount) {
            $transactions->append($bankAccount->getTransactions());
        }
        $view = new View('transaction');
        echo $view->addData('transactions', $transactions)->render();
    }

    /**
     * Display the Web-page for /Transaction/deposit
     */
    public function createTransDepositPage(int $fromAccountID){
        $collection = new TransactionCollectionModel();
        $transactions = $collection->getTransactions();
        $view = new View('transDeposit');
        echo $view->addData('transactions', $transactions)->render();
    }


    /**
     * Class handleTransfer
     *
     * @param UserAccountModel $user
     * @param int $fromAccountID
     * @return null|string
     */
    private function handleTransfer(UserAccountModel $user, int $fromAccountID, $toAccountStr, $amountStr): ?string
    {
        $fromAccount = $user->getBankAccountByID($fromAccountID);
        if($fromAccount === null) {
            //the user doesn't own the bank account they are trying to transfer money from
            return 'Unable to access the account '.$fromAccountID.' please try again or contact customer support';
        }
        $toAccountID = filter_var($toAccountStr, FILTER_VALIDATE_INT);
        $toAccount = $toAccountID === FALSE ? null : (new BankAccountModel())->load($toAccountID);
        if($toAccount === null) {
            return "Invalid account ID to transfer to";
        }
        $amount = intval(floatval($amountStr) * 100);
        if($amount < 0) {
            return "Cannot transfer a negative amount";
        }
        $newFromAmount = $fromAccount->getBalance() - $amount;
        if($newFromAmount < $fromAccount->getMinimumAllowedBalance()) {
            return "Your bank account's balance is too low";
        }
        $newToAmount = $toAccount->getBalance() + $amount;
        $toAccount->setBalance($newToAmount);
        $fromAccount->setBalance($newFromAmount);
        $toAccount->save();
        static::saveTransaction($toAccountID, $amount, "D");
        $fromAccount->save();
        static::saveTransaction($fromAccount->getId(), $amount, "W");
        return null;
    }

    /**
     * Display the Web-page for /Transaction/transfer
     * @param int $fromAccountID The id of the account to transfer money from
     */
    public function createTransTransferPage(int $fromAccountID) {
        $user = UserAccountController::getCurrentUser();
        if($user !== null) {
            if(isset($_GET['submit'])) {
                $toAccountStr = $_GET["toAccount"];
                $amountStr = $_GET["amount"];
                $error = $this->handleTransfer($user, $fromAccountID, $toAccountStr, $amountStr);
                if($error === null) {
                    $okLocation = static::getUrl("showAccounts");
                    $this->redirect('transactionSuccess', ['message'=>"transfer successful", 'okLocation'=>$okLocation]);
                } else {
                    $view = new View('transTransfer');
                    $view->addData('fromAccountID', $fromAccountID);
                    $view->addData('error', $error);
                    echo $view->render();
                }
            } else {
                $view = new View('transTransfer');
                echo $view->addData('fromAccountID', $fromAccountID)->render();
            }
        }
    }

    /**
     * Class: Handle Withdrawal
     *
     * @param UserAccountModel $user
     * @param int $fromAccountID
     * @param $amount, the amount withdrawn
     */

    public function handleWithdrawal(UserAccountModel $user, int $fromAccountID, $amount){

    }

    /**
     * Display the Web-page for /Transaction/withdrawal
     * @param int $fromAccountID The id of the account to transfer money from
     */
    public function createTransWithdrawalPage(int $fromAccountID){
        $user = UserAccountController::getCurrentUser();
        if($user !== null){
            if(isset($_GET['withdrawal'])){
                $amountWithdrawn = $_GET('wAmount');
                $error = $this->handleWithdrawal($user, $fromAccountID, $amountWithdrawn);
                if($error === null) {
                    $okLocation = static::getUrl("showAccounts");
                    $this->redirect('transactionSuccess', ['message'=>"withdrawal successful", 'okLocation'=>$okLocation]);
                }
                else{
                    $view = new View('transWithdrawal');
                    $view->addData('fromAccountID', $fromAccountID);
                    $view->addData('error', $error);
                    echo $view->render();
                }
            }
            else {
                $view = new View('transWithdrawal');
                echo $view->addData('fromAccount', $fromAccountID)->render();
            }
        }
    }

    /**
     * save Transaction
     * @param int $accountID The id of the bank account that was effected by the transaction
     * @param int $amount The amount of the transaction in cents
     * @param string $type Can be 'W' = Withdrawal or 'D' = Deposit
     */
    private static function saveTransaction(int $accountID, int $amount, string $type)
    {
        $transaction = new TransactionModel();
        $transaction->setTime(new \DateTime());
        $transaction->setAccountID($accountID);
        $transaction->setAmount($amount);
        $transaction->setType($type);
        $transaction->save();
    }
}

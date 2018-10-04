<?php
namespace agilman\a2\controller;

use agilman\a2\model\BankAccountModel;
use agilman\a2\model\TransactionModel;
use agilman\a2\model\UserAccountModel;
use agilman\a2\view\View;

/**
 * Class TransController
 *
 * @author Isaac Clancy, Junyi Chen, Sven Gerhards
 */
class TransController extends Controller
{
    /**
     * Display the Web-page of /Transactions/
     */
    public function createTransactionsPage(){
        /**
         * @var UserAccountModel $user This is used for user authenticity
         */
        $user = UserAccountController::getCurrentUser();
        if($user === null) {
            //unauthorised user
            return;
        }
        if(isset($_GET['bankAccountID'])) {
            $bankAccountID = $_GET['bankAccountID'];
            $bankAccount = $user->getBankAccountByID($bankAccountID);
            if($bankAccount != null) {
                $transactions = $bankAccount->getTransactions();
                $view = new View('transaction');
                $view->addData('transactions', $transactions);
                echo $view->render();
            } else {
                $this->redirect('showAccounts');
            }
        } else {
            $transactions = $user->getTransactions();
            $view = new View('transaction');
            $view->addData('transactions', $transactions);
            echo $view->render();
        }
    }

    /**
     * Account to create deposit page
     * @param int $id Account id to be deposited
     */
    public function createDepositPage($id){
        $user = UserAccountController::getCurrentUser();
        if($user == null) {
            return;
        }
        $bankAccount = $user->getBankAccountByID($id);
        if($bankAccount !== null) {
            $view = new View('transDeposit');
            $view->addData('accountId', $id);
            echo $view->render();
        }
    }

    /**
     *  This function do the deposit, it will first check correct user information
     *  then check account existence
     * @param int $id
     */
    public function depositPage(int $id){

        $user = UserAccountController::getCurrentUser();
        if($user == null) {
            //incorrect user information
            return;
        }
        $bankAccount = $user->getBankAccountByID($id);
        if($bankAccount !== null) {
            if(isset($_POST['submit'])) {
                //correct user information and account number
                $amount = intval(floatval($_POST['amount']) * 100);
                $balance = $bankAccount->getBalance() + $amount;
                $bankAccount->setBalance($balance);

                $bankAccount->save();
                static::generateTransaction($bankAccount->getId(), $amount, "D");

                $view = new View("transDepositMessage");
                $view->addData("balance", $bankAccount->getBalance() / 100);
                $view->addData("fromAccountID",$id);
                echo $view->render();
            } else {
                $view = new View('transDeposit');
                $view->addData('fromAccountID', $id);
                echo $view->render();
            }
        } else {
            $this->redirect("login");
        }
    }


    /**
     * Transfers money from one account to another if possible.
     *
     * @param UserAccountModel $user The current user
     * @param int $fromAccountID The id of the bank account to remove money from.
     * An error will be returned if the current user doesn't own this bank account
     * @param string $toAccountStr The id of the bank account to add money to
     * @param string $amountStr The amount of money to transfer in dollars
     * @return null|string An error message if an error occurred, null otherwise
     */
    private function handleTransfer(UserAccountModel $user, int $fromAccountID, string $toAccountStr, string $amountStr): ?string
    {
        $fromAccount = $user->getBankAccountByID($fromAccountID);
        if($fromAccount === null) {
            //the user doesn't own the bank account they are trying to transfer money from
            return 'Unable to access the account '.$fromAccountID.' please try again or contact customer support';
        }
        $toAccountID = filter_var($toAccountStr, FILTER_VALIDATE_INT);
        $toAccount = $toAccountID === false ? null : (new BankAccountModel())->load($toAccountID);
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
        static::generateTransaction($toAccountID, $amount, "D");
        $fromAccount->save();
        static::generateTransaction($fromAccount->getId(), $amount, "W");
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
                    $this->transactionSuccessful("Transfer successful", $okLocation);
                } else {
                    $view = new View('transTransfer');
                    $view->addData('fromAccountID', $fromAccountID);
                    $view->addData('error', $error);
                    $view->addData('amount', $amountStr);
                    $view->addData('toAccount', $toAccountStr);
                    echo $view->render();
                }
            } else {
                $view = new View('transTransfer');
                echo $view->addData('fromAccountID', $fromAccountID)->render();
            }
        }
    }

    /**
     * Removes money from a bank account
     *
     * @param UserAccountModel $user
     * @param int $fromAccountID
     * @param int $amountWithdrawn
     * @return null|string The error message if an error occurred, null otherwise
     */

    public function handleWithdrawal(UserAccountModel $user, int $fromAccountID, int $amountWithdrawn){
        $fromAccount = $user->getBankAccountByID($fromAccountID);
        if($fromAccount === null) {
            //the user doesn't own the bank account they are trying to transfer money from
            return 'Unable to access the account '.$fromAccountID.' please try again or contact customer support';
        }
        if($amountWithdrawn < 0) {
            return "Cannot withdrawn a negative amount";
        }
        $newBalance = $fromAccount->getBalance() - $amountWithdrawn;
        if($newBalance < $fromAccount->getMinimumAllowedBalance()) {
            return "Your bank account's balance is too low";
        }
        $fromAccount->setBalance($newBalance);
        $fromAccount->save();
        static::generateTransaction($fromAccount->getId(), $amountWithdrawn, "W");
        return null;
    }

    /**
     * Display the Web-page for /Transaction/withdrawal
     * @param int $fromAccountID The id of the account to transfer money from
     */
    public function createTransWithdrawalPage(int $fromAccountID){
        $user = UserAccountController::getCurrentUser();
        if($user === null) {
            return;
        }
        if(isset($_GET['withdrawal'])){
            $amountWithdrawn = intval(floatval($_GET['wAmount']) * 100);
            $error = $this->handleWithdrawal($user, $fromAccountID, $amountWithdrawn);
            if($error === null) {
                $okLocation = static::getUrl("showAccounts");
                $this->transactionSuccessful("Withdrawal successful", $okLocation);
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
            echo $view->addData('fromAccountID', $fromAccountID)->render();
        }
    }

    /**
     * Displays the transaction successful web page
     * @param string $message A message to let the user know what transaction succeeded
     * @param string $okLocation The location to go to when when the user clicks the ok button
     */
    private function transactionSuccessful(string $message, string $okLocation)
    {
        $view = new View("successMessage");
        $view->addData('message', $message);
        $view->addData('okLocation', $okLocation);
        echo $view->render();
    }

    /**
     * Creates a transaction and saves it to the database
     * @param int $accountID The id of the bank account that was effected by the transaction
     * @param int $amount The amount of the transaction in cents
     * @param string $type Can be 'W' = Withdrawal or 'D' = Deposit
     */
    private static function generateTransaction(int $accountID, int $amount, string $type)
    {
        $transaction = new TransactionModel();
        $transaction->setTime(new \DateTime());
        $transaction->setAccountID($accountID);
        $transaction->setAmount($amount);
        $transaction->setType($type);
        $transaction->setUserID(UserAccountController::getCurrentUser()->getId());
        $transaction->save();

    }
}

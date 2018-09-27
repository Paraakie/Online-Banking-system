<?php
namespace agilman\a2\controller;

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
     * createTransIndexPage
     */
    public function createTransIndexPage(){
        $collection = new TransactionCollectionModel();
        $transactions = $collection->getTransactions();
        $view = new View('transIndex');
        echo $view->addData('transactions', $transactions)->render();
    }


}

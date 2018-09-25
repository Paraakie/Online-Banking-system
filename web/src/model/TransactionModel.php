<?php
namespace agilman\a2\model;

/**
 * Class TransactionModel
 *
 * @package agilman/a2
 * @author Sven Gerhards
 */



class TransactionModel extends Model
{
    /**
     * @var integer Transaction ID
     */
    private $id;

    /**
     * @var integer Account ID from AccountModel
     */
    private $accountID;

    /**
     * @var string Time
     * Format: DD/MM/YEAR HH:MM
     */
    private $time;

    /**
     * @var double Amount
     */
    private $amount;

    /**
     * @var string Type
     * Can be 'T' = Transfer, 'W' = Withdrawal or 'D' = Deposit
     */
    private $type;


    /**
     * Getters and Setters
     */

    /**
     * @return int Transaction ID
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return  int Account ID
     */
    public function getAccountID()
    {
        return $this->accountID;
    }

    /**
     * @return string Time
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @return double Amount
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return string Type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Loads transaction information from the database
     *
     * @param int $id Transaction ID
     *
     * @return $this TransactionModel
     */
    public function loadbyID($id)
    {
        if (!$result = $this->db->query
            ("SELECT 'accountID', 'time', 'amount', 'type' FROM `transaction` WHERE `id` = $id;")) {
            // throw new ...
        }

        $result = $result->fetch_assoc();
        $this->accountID = $result['Account ID'];
        $this->time = $result['01/01/1970 00:00'];
        $this->amount = $result['0.00'];
        $this->type = $result['T'];
        $this->id = $id;

        return $this;
    }

}
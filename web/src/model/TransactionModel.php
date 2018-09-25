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
    public function loadByID($id)
    {
        if (!$result = $this->db->query(
            "SELECT `accountID`, `time`, `amount`, `type` FROM `transaction` WHERE `id` = $id;")) {
            exit();
        }

        $result = $result->fetch_assoc();
        $this->accountID = $result['accountID'];
        $this->time = $result['time'];
        $this->amount = $result['amount'];
        $this->type = $result['type'];
        $this->id = $id;

        return $this;
    }

    /**
     * Saves account information to the database
     * @return $this TransactionModel
     */
    public function save(): TransactionModel
    {
        if (!isset($this->id)) {
            if (!$stm = $this->db->prepare(
                "INSERT INTO `transaction`(`accountID`, `time`, `amount`, `type`) VALUES(?, ?, ?, ?)")) {
                exit();
            }
            $stm->bind_param("ssss", $this->accountID, $this->time, $this->amount, $this->type);
            $result = $stm->execute();
            $stm->close();
            if (!$result) {
                exit("Failed to execute prepared statement");
            }
            $stm->close();
            $this->id = $this->db->insert_id;
        } else {
            // saving existing account - perform UPDATE
            if (!$stm = $this->db->prepare(
                "UPDATE `transaction` SET `accountID`=?, `time`=?, `amount`=?, `type`=? WHERE `id`=?;")) {
                exit();
            }
            $stm->bind_param("ssssi", $this->accountID, $this->time, $this->amount, $this->type, $this->id);
            $result = $stm->execute();
            $stm->close();
            if (!$result) {
                exit("Failed to execute prepared statement");
            }
        }

        return $this;
    }
}
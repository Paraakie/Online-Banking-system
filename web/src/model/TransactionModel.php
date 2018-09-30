<?php
namespace agilman\a2\model;

use DateTime;

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
     * @var integer Account ID from BankAccountModel
     */
    private $accountID;

    /**
     * @var DateTime Time
     */
    private $time;

    /**
     * @var int Amount
     */
    private $amount;

    /**
     * @var string Type
     * Can be 'T' = Transfer, 'W' = Withdrawal or 'D' = Deposit
     */
    private $type;


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

        $data = $result->fetch_assoc();
        $this->accountID = intval($data['accountID']);
        $this->time = new DateTime($data['time']);
        $this->amount = $data['amount'];
        $this->type = $data['type'];
        $this->id = $id;
        $result->close();

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
            $stm->bind_param(
                "isis",
                $this->accountID,
                $this->time->format('Y-m-d H:i:s'),
                $this->amount,
                $this->type
            );
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
            $stm->bind_param(
                "isisi",
                $this->accountID,
                $this->time->format('Y-m-d H:i:s'),
                $this->amount,
                $this->type,
                $this->id
            );
            $result = $stm->execute();
            $stm->close();
            if (!$result) {
                exit("Failed to execute prepared statement");
            }
        }

        return $this;
    }

    /**
     * Getters and Setters
     */

    /**
     * @return int Transaction ID
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return  int Account ID
     */
    public function getAccountID(): int
    {
        return $this->accountID;
    }

    /**
     * @return DateTime The time the transaction was created
     */
    public function getTime(): DateTime
    {
        return $this->time;
    }

    /**
     * @return int Amount in cents
     */
    public function getAmount(): int
    {
        return $this->amount;
    }

    /**
     * @return string Type
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param int $accountID The bank account the created the transaction
     */
    public function setAccountID(int $accountID): void
    {
        $this->accountID = $accountID;
    }

    /**
     * @param DateTime $time
     */
    public function setTime(DateTime $time): void
    {
        $this->time = $time;
    }

    /**
     * @param int $amount Amount in cents
     */
    public function setAmount(int $amount): void
    {
        $this->amount = $amount;
    }

    /**
     * @param string $type Can be 'T' = Transfer, 'W' = Withdrawal or 'D' = Deposit
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }
}
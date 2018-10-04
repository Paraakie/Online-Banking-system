<?php

namespace jis\a2\model;

use DateTime;

/**
 * Stores all the information about a transaction and handles saving it to a database
 *
 * @package jis/a2
 * @author Isaac Clancy, Junyi Chen, Sven Gerhards
 */
class TransactionModel extends Model
{
    /**
     * @var int Transaction ID, unique for a transaction
     */
    private $id;

    /**
     * @var int Account ID from BankAccountModel
     */
    private $accountID;

    /**
     * @var int User ID
     */
    private $userID;

    /**
     * @var DateTime Time of creation
     */
    private $time;

    /**
     * @var int Amount in cents
     */
    private $amount;

    /**
     * @var string Type
     * Can be 'W' = Withdrawal or 'D' = Deposit
     */
    private $type;


    /**
     * Loads transaction information from the database
     *
     * @param int $id Transaction ID
     *
     * @return $this TransactionModel this if successful, null otherwise
     */
    public function loadByID($id): ?TransactionModel
    {
        if (!$result = $this->db->query(
            "SELECT `accountID`, `userID`, `time`, `amount`, `type` FROM `transactions` WHERE `id` = $id;"
        )) {
            die($this->db->error);
        }

        $data = $result->fetch_assoc();
        if ($data === null) {
            return null;
        }
        $this->accountID = intval($data['accountID']);
        $this->userID = intval($data['userID']);
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
                "INSERT INTO `transactions`(`accountID`, `userID`, `time`, `amount`, `type`)
                    VALUES(?, ?, ?, ?, ?)"
            )) {
                die($this->db->error);
            }
            $formattedDateTime = $this->time->format('Y-m-d H:i:s');
            $result = $stm->bind_param(
                "iisis",
                $this->accountID,
                $this->userID,
                $formattedDateTime,
                $this->amount,
                $this->type
            );
            if (!$result) {
                die($this->db->error);
            }
            $result = $stm->execute();
            $stm->close();
            if (!$result) {
                die($this->db->error);
            }
            $this->id = $this->db->insert_id;
        } else {
            // saving existing account - perform UPDATE
            if (!$stm = $this->db->prepare(
                "UPDATE `transactions` SET `accountID`=?, `userID`=?, `time`=?, `amount`=?, `type`=?
                    WHERE `id`=?;"
            )) {
                die($this->db->error);
            }
            $stm->bind_param(
                "iisisi",
                $this->accountID,
                $this->userID,
                $this->time->format('Y-m-d H:i:s'),
                $this->amount,
                $this->type,
                $this->id
            );
            $result = $stm->execute();
            $stm->close();
            if (!$result) {
                die($this->db->error);
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
     * @return int User ID
     */
    public function getUserID(): int
    {
        return $this->userID;
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
     * @param int $userID The current user's ID
     */
    public function setUserID(int $userID): void
    {
        $this->userID = $userID;
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
     * @param string $type Can be 'W' = Withdrawal or 'D' = Deposit
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }
}

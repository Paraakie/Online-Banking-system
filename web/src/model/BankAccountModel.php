<?php

namespace jis\a2\model;


/**
 * Stores the information about a bank account and handles saving it to a database
 *
 * @package jis/a2
 * @author  Andrew Gilman <a.gilman@massey.ac.nz>
 * @author  Isaac Clancy, Junyi Chen, Sven Gerhards
 */
class BankAccountModel extends Model
{
    /**
     * @var int Account ID
     */
    private $id;

    /**
     * @var string Account Name
     */
    private $name;

    /**
     * @var int Balance in cents
     */
    private $balance;

    /**
     * @var int Owner's user ID
     */
    private $userID;


    /**
     * @return int Account ID, unique to a bank account
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int The id of the user that owns this account
     */
    public function getUserId(): int
    {
        return $this->userID;
    }

    /**
     * @return string The name given to an account by the user
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int balance in cents
     */
    public function getBalance(): int
    {
        return $this->balance;
    }

    /**
     * Gets all transactions made with the account
     */
    public function getTransactions(): \Generator
    {
        if (!$result = $this->db->query("SELECT `id` FROM `transactions` WHERE `accountID`=$this->id;")) {
            die($this->db->error);
        }
        $transactionIds = array_column($result->fetch_all(), 0);
        foreach ($transactionIds as $id) {
            // Use a generator to save on memory/resources
            // load accounts from DB one at a time only when required
            yield (new TransactionModel())->loadByID($id);
        }
    }

    /**
     * @return int The minimum balance that a user can reduce their balance to by making transactions.
     * The balance can still go below this from fees etc.
     */
    public function getMinimumAllowedBalance(): int
    {
        return 0;
    }

    /**
     * @param string $name Account name
     *
     * @return $this BankAccountModel
     */
    public function setName(string $name): BankAccountModel
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @param int $balance The new balance in cents
     * @return $this BankAccountModel
     */
    public function setBalance(int $balance): BankAccountModel
    {
        $this->balance = $balance;
        return $this;
    }

    /**
     * @param int $userID The id of the user that has this account
     * @return $this BankAccountModel
     */
    public function setUserId(int $userID): BankAccountModel
    {
        $this->userID = $userID;
        return $this;
    }

    /**
     * Loads account information from the database
     *
     * @param int $id Account ID
     *
     * @return $this BankAccountModel
     */
    public function load($id): ?BankAccountModel
    {
        if (!$result = $this->db->query("SELECT `name`, `balance`, `userID` FROM `bank_accounts` WHERE `id` = $id;")) {
            die($this->db->error);
        }

        $data = $result->fetch_assoc();
        if ($data === null) {
            return null;
        }
        $this->name = $data['name'];
        $this->balance = intval($data['balance']);
        $this->userID = intval($data['userID']);
        $this->id = $id;

        return $this;
    }

    /**
     * Saves account information to the database
     * @return $this BankAccountModel
     */
    public function save(): BankAccountModel
    {
        if (!isset($this->id)) {
            // New account - Perform INSERT
            if (!$stm = $this->db->prepare("INSERT INTO `bank_accounts` VALUES (NULL, ?, ?, ?);")) {
                die($this->db->error);
            }
            $stm->bind_param("sii", $this->name, $this->balance, $this->userID);
            $result = $stm->execute();
            $stm->close();
            if (!$result) {
                die($this->db->error);
            }
            $this->id = $this->db->insert_id;
        } else {
            // saving existing account - perform UPDATE
            if (!$stm = $this->db->prepare("UPDATE `bank_accounts` SET `name`=?, `balance`=?,
                    `userID`=? WHERE `id` = $this->id;")) {
                die($this->db->error);
            }
            $stm->bind_param("sii", $this->name, $this->balance, $this->userID);
            $result = $stm->execute();
            $stm->close();
            if (!$result) {
                die($this->db->error);
            }
        }

        return $this;
    }

    /**
     * Deletes account from the database
     * @return $this BankAccountModel
     */
    public function delete(): BankAccountModel
    {
        if (!$result = $this->db->query("DELETE FROM `bank_accounts` WHERE `id` = $this->id;")) {
            die($this->db->error);
        }

        return $this;
    }
}

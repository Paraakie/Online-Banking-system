<?php
namespace agilman\a2\model;


/**
 * Class BankAccountModel
 *
 * @package agilman/a2
 * @author  Andrew Gilman <a.gilman@massey.ac.nz>
 */
class BankAccountModel extends Model
{
    /**
     * @var integer Account ID
     */
    private $id;
    /**
     * @var string Account Name
     */
    private $name;

    private $balance;
    private $userID;


    /**
     * @return int Account ID
     */
    public function getId()
    {
        return $this->id;
    }

    public function getUserId(){
        return $this->userID;
    }
    /**
     * @return string Account Name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return int Account balance in cents
     */
    public function getBalance(){
        return $this->balance;
    }

    /**
     * Gets all transactions make with the account
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
    public function setName(string $name)
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

    public function setUserId(int $userID): void
    {
        $this->userID = $userID;
    }

    /**
     * Loads account information from the database
     *
     * @param int $id Account ID
     *
     * @return $this BankAccountModel
     */
    public function load($id)
    {
        if (!$result = $this->db->query("SELECT `name`, `balance`, `userID` FROM `bank_accounts` WHERE `id` = $id;")) {
            die($this->db->error);
        }

        $result = $result->fetch_assoc();
        $this->name = $result['name'];
        $this->balance = intval($result['balance']);
        $this->userID = intval($result['userID']);
        $this->id = $id;

        return $this;
    }

    /**
     * Saves account information to the database

     * @return $this BankAccountModel
     */
    public function save()
    {
        if (!isset($this->id)) {
            // New account - Perform INSERT
            if(!$stm = $this->db->prepare("INSERT INTO `bank_accounts` VALUES (NULL, ?, ?, ?);")) {
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
            if(!$stm = $this->db->prepare("UPDATE `account` SET `name`=?, `balance`=?, `userID`=? WHERE `id` = $this->id;")) {
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
    public function delete()
    {
        if (!$result = $this->db->query("DELETE FROM `bank_accounts` WHERE `bank_accounts`.`id` = $this->id;")) {
            die($this->db->error);
        }

        return $this;
    }
}

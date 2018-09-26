<?php
/**
 * Created by PhpStorm.
 * User: Isaac
 * Date: 25/09/2018
 * Time: 12:39 PM
 */

namespace agilman\a2\model;


class UserAccountModel extends Model
{
    private $id;
    private $name;
    private $password;

    /**
     * Loads the user account with the given id
     * @param $id int The account id
     * @return $this The loaded account
     */
    public function loadByID(int $id)
    {
        if (!$result = $this->db->query("SELECT `name`, `password` FROM `user_accounts` WHERE `id`=$id;")) {
            return null;
        }

        $result = $result->fetch_assoc();
        $this->name = $result['name'];
        $this->password = $result['password'];
        $this->id = $id;

        return $this;
    }

    /**
     * Loads the user account with the given name and password.
     * The password should be hashed already.
     * @param $name string The account name
     * @param $password string The account password
     * @return UserAccountModel An account if the account exists, null otherwise
     */
    public function loadByNameAndPassword(string $name, string $password): ?UserAccountModel
    {
        if (!$selectAccountByNameAndPassword = $this->db->prepare(
            "SELECT `id` FROM `user_accounts` WHERE `name`=? AND `password`=?;")) {

            exit("Failed to make prepared statement");
        }
        $selectAccountByNameAndPassword->bind_param("ss", $name, $password);
        if (!$result = $selectAccountByNameAndPassword->execute()) {
            $selectAccountByNameAndPassword->close();
            // throw new ...
            exit("Failed to execute prepared statement");
        }
        $selectAccountByNameAndPassword->bind_result($id);
        if($selectAccountByNameAndPassword->fetch()) {
            $this->name = $name;
            $this->password = $password;
            $this->id = $id;

            $selectAccountByNameAndPassword->close();
            return $this;
        }
        $selectAccountByNameAndPassword->close();
        return null;
    }

    /**
     * Loads the user account with the given name.
     * @param $name string The account name
     * @return UserAccountModel An account if the account exists, null otherwise
     */
    public function loadByName(string $name): ?UserAccountModel
    {
        if (!$selectAccountByName = $this->db->prepare(
            "SELECT `id`, `password` FROM `user_accounts` WHERE `name`=?;")) {

            exit("Failed to make prepared statement");
        }
        $selectAccountByName->bind_param("s", $name);
        if (!$result = $selectAccountByName->execute()) {
            $selectAccountByName->close();
            // throw new ...
            exit("Failed to execute prepared statement");
        }

        $selectAccountByName->bind_result($id, $password);
        $result = $selectAccountByName->fetch();
        $selectAccountByName->close();
        if($result) {
            $this->name = $name;
            $this->password = $password;
            $this->id = $id;

            return $this;
        }
        return null;
    }

    /**
     * Saves user account information to the database. Creates an id if the account doesn't have one already.
     * name and password must not be null.
     * @return $this UserAccountModel
     */
    public function save()
    {
        $name = $this->name;
        $password = $this->password;
        if (!isset($this->id)) {
            if (!$stm = $this->db->prepare("INSERT INTO `user_accounts`(`name`, `password`) VALUES(?, ?)")) {
                exit();
            }
            $stm->bind_param("ss", $name, $password);
            $result = $stm->execute();
            $stm->close();
            if (!$result) {
                exit("Failed to execute prepared statement");
            }
            $this->id = $this->db->insert_id;
        } else {
            // saving existing account - perform UPDATE
            if (!$stm = $this->db->prepare("UPDATE `user_accounts` SET `name`=?, `password`=? WHERE `id`=?;")) {
                exit();
            }
            $stm->bind_param("ssi", $name, $password, $this->id);
            $result = $stm->execute();
            $stm->close();
            if (!$result) {
                exit("Failed to execute prepared statement");
            }
        }

        return $this;
    }

    /**
     * Gets the account password
     * @return string A hash of the account password
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * Sets the account password
     * @param string $password A hash of the account password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * Gets the account name
     * @return string The account name
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Sets the account name
     * @param string $name The new account name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * Returns a unique id for the account. The account must have been saved to have an id.
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }
}
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
    //An sql prepared statement for getting the account with a given name and password

    /**
     * Loads the user account with the given id
     * @param $id int The account id
     * @return $this The loaded account
     */
    public function loadByID($id)
    {
        if (!$result = $this->db->query("SELECT `name`, `password` FROM `user_accounts` WHERE `id`=$id;")) {
            // throw new ...
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
    public function loadByNameAndPassword(string $name, string $password): UserAccountModel
    {
        if (!$selectAccountByNameAndPassword = $this->db->prepare("SELECT `id` FROM `user_accounts` WHERE name=? AND password=?;")) {
            // throw new ...
        }
        $selectAccountByNameAndPassword->bind_param("ss", $name, $password);
        if (!$result = $selectAccountByNameAndPassword->execute()) {
            $selectAccountByNameAndPassword->close();
            // throw new ...
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
}
<?php

namespace Models;

use Config\Database;

class User
{
    public int $iduser;
    public string $name;
    public string $email;
    public int $status;

    /**
     * find
     *
     * @param  string $columns
     * @param  array $filters
     * @return array|null
     */
    public static function find(string $columns = "*", array $filters = [])
    {
        $sql = "SELECT " . $columns . " FROM user  ";
        if (!empty($filters)) {
            $sql .= " WHERE ";
            $count = 0;
            foreach ($filters as $column => $value) {
                if ($count > 0) {
                    $sql .= " AND ";
                }
                $sql .= $column . " = :" . $column;
                $count++;
            }
        }
        return Database::getResults($sql, $filters);
    }

    /**
     * insert
     *
     * @return boolean|int
     */
    public function insert()
    {
        $sql = " INSERT INTO `user`
        (`iduser`, `name`, `email`)
        VALUES 
        (:iduser,     :name,   :email);";
        $values = [
            "name" => $this->name,
            "email" => $this->email,
            "iduser" => $this->iduser
        ];
        return Database::operation($sql, $values);
    }


    /**
     * update
     *
     * @return boolean
     */
    public function update()
    {
        $sql = "UPDATE `user` SET 
        `name` = :name, `email` = :email
        WHERE `iduser` = :iduser";
        $values = [
            "name" => $this->name,
            "email" => $this->email,
            "iduser" => $this->iduser
        ];
        return Database::operation($sql, $values);
    }


    /**
     * delete
     *
     * @return boolean
     */
    public function delete()
    {
        $sql = "UPDATE `user` SET `status` = 0
        WHERE `iduser` = :iduser";
        $values = [
            "iduser" => $this->iduser
        ];
        return Database::operation($sql, $values);
    }
}

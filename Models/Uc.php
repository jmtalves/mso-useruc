<?php

namespace Models;

use Config\Database;

class Uc
{
    public int $iduc;
    public string $code;
    public string $name;
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
        $sql = "SELECT " . $columns . " FROM `uc`  ";
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
        $sql = " INSERT INTO `uc`
        (`iduc`, `code`, `name`)
        VALUES 
        (:iduc,     :code,   :name)";
        $values = [
            "iduc" => $this->iduc,
            "code" => $this->code,
            "name" => $this->name
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
        $sql = "UPDATE `uc` SET 
        `code` = :code, `name` = :name
        WHERE `iduc` = :iduc";
        $values = [
            "code" => $this->code,
            "name" => $this->name,
            "iduc" => $this->iduc
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
        $sql = "UPDATE `uc` SET `status` = 0
        WHERE `iduc` = :iduc";
        $values = [
            "iduc" => $this->iduc
        ];
        return Database::operation($sql, $values);
    }
}

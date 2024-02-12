<?php

namespace Models;

use Config\Database;

class UserUc
{
    public int $iduc;
    public int $iduser;
    //@var int $status 2=>payment pending, 1=>register success, 0=>cancel
    public int $status;
    public string $created_at;
    public string $updated_at;

    /**
     * find
     *
     * @param  string $columns
     * @param  array $filters
     * @return array|null
     */
    public static function find(string $columns = "*", array $filters = [])
    {
        $filters_converted = [];
        $sql = "SELECT " . $columns . " FROM `user_uc` as uuc 
                                        left join  user on user.iduser=uuc.iduser 
                                        left join uc on uc.iduc=uuc.iduc  
                                        ";
        if (!empty($filters)) {
            $sql .= " WHERE ";
            $count = 0;
            foreach ($filters as $column => $value) {
                $filters_converted[str_replace(".", "_", $column)] = $value;
                if ($count > 0) {
                    $sql .= " AND ";
                }
                $sql .=  $column . " = :" . str_replace(".", "_", $column);
                $count++;
            }
        }
        return Database::getResults($sql, $filters_converted);
    }

    /**
     * insert
     *
     * @return boolean
     */
    public function insert()
    {
        $sql = " INSERT INTO `user_uc`
        (`iduc`, `iduser`)
        VALUES 
        (:iduc,     :iduser)";
        $values = [
            "iduc" => $this->iduc,
            "iduser" => $this->iduser
        ];
        return Database::operation($sql, $values);
    }




    /**
     * update Status
     *
     * @return boolean
     */
    public function updateStatus()
    {
        $sql = "UPDATE `user_uc` SET `status` = :status
        WHERE `iduser` = :iduser and `iduc` = :iduc";
        $values = [
            "iduc" => $this->iduc,
            "iduser" => $this->iduser,
            "status" => $this->status
        ];
        return Database::operation($sql, $values);
    }
}

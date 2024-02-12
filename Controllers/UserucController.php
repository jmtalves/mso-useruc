<?php

namespace Controllers;

use Models\Uc;
use Models\User;
use Models\UserUc;
use Libraries\Response;
use Libraries\Request;

class UserucController
{
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        $user = Request::verifyToken([0, 1]);
        $filter = [];
        if ($user[0]->type == 1) { //if no admin only see uc in which you are registered
            $filter["uuc.iduser"] = $user[0]->iduser;
        }
        $info = UserUc::find("uuc.*, uc.code as ucCode, uc.name as ucName, user.email as userEmail", $filter);
        if (empty($info)) {
            Response::sendResponse(200, ["msg" => "No Data Found", "info" => []]);
        }
        Response::sendResponse(200, ["msg" => "Data Found", "info" => $info]);
    }

    /**
     * insert
     *
     * @return void
     */
    public function insert()
    {
        Request::verifyToken([0, 1]);
        $post = Request::getPostParams();
        if (empty($post['user']) || empty($post['uc'])) {
            Response::sendResponse(422, ["msg" => "Parameters not found"]);
        }
        $response = $this->save($post);
        if ($response) {
            Response::sendResponse(200, ["msg" => "Inserted Success"]);
        } else {
            Response::sendResponse(422, ["msg" => "Error on insert record"]);
        }
    }


    /**
     * save
     *
     * @param  array $post
     * @return boolean
     */
    private function save(array $post)
    {
        $uuc_class = new UserUc();
        $user = User::find("*", ["email" => $post['user']]);
        $uc = Uc::find("*", ["code" => $post['uc']]);
        if (!$user || !$uc) {
            return false;
        }
        $uuc_class->iduc = $uc[0]->iduc;
        $uuc_class->iduser = $user[0]->iduser;
        if ($useruc_old = UserUc::find("uuc.*", ["uuc.iduc" => $uc[0]->iduc, "uuc.iduser" => $user[0]->iduser])) {
            if ($useruc_old[0]->status != 0) {
                Response::sendResponse(205, ["msg" => "User Already Register in Uc"]);
            }
            $uuc_class->status = 2;
            return $uuc_class->updateStatus();
        }
        return $uuc_class->insert();
    }


    /**
     * checkInfo
     *
     * @param  array $params
     * @return array|boolean
     */
    private function checkInfo(array $params = [])
    {
        if (empty($params)) {
            Response::sendResponse(422, ["msg" => "Parameters not found"]);
        }
        $user = User::find("*", ["email" => $params[0]]);
        $uc = Uc::find("*", ["code" => $params[1]]);
        if (!$user || !$uc) {
            return false;
        }
        $us = UserUc::find("*", ['uuc.iduser' => $user[0]->iduser, 'uuc.iduc' => $uc[0]->iduc]);
        return $us;
    }


    /**
     * update
     *
     * @param  array $params
     * @return void
     */
    public function update(array $params = [])
    {
        $post = Request::getPostParams();
        if (empty($post['status'])) {
            Response::sendResponse(422, ["msg" => "Parameters not found"]);
        }
        $this->changeStatus($params, $post['status']);
    }

    /**
     * delete
     *
     * @param  array $params
     * @return void
     */
    public function delete(array $params = [])
    {
        $this->changeStatus($params, 0);
    }


    /**
     * change Status
     *
     * @param  array $params
     * @return void
     */
    private function changeStatus(array $params = [], int $status = 2)
    {
        Request::verifyToken([0]);
        $us = $this->checkInfo($params);
        if (!$us) {
            Response::sendResponse(404, ["msg" => "Data Not Found"]);
        }
        $uuc_class = new UserUc();
        $uuc_class->iduser = $us[0]->iduser;
        $uuc_class->iduc = $us[0]->iduc;
        $uuc_class->status = $status;
        if ($uuc_class->updateStatus()) {
            Response::sendResponse(200, ["msg" => "Success"]);
        } else {
            Response::sendResponse(422, ["msg" => "Error on record"]);
        }
    }
}

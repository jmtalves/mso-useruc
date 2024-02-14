<?php

namespace Controllers;

use Models\Uc;
use Models\User;
use Models\UserUc;
use Libraries\Response;
use Libraries\Request;
use Libraries\MessageBroker;
use Exception;

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
     * insert data
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
        $userUcInfo = $this->save($post);
        //As there won't be an actual payment,
        //let's leave the possibility to refuse payment and thereby test the SAGA pattern.
        $simulate_fail = isset($post['fail']) ? $post['fail'] : 0;
        MessageBroker::sendMessage(
            "createPayment",
            [
                "simulate_fail" => $simulate_fail,
                "iduser" => $userUcInfo->iduser,
                "iduc" => $userUcInfo->iduc
            ]
        );
        Response::sendResponse(200, ["msg" => "Inserted Success"]);
    }


    /**
     * save
     *
     * @param  array $post
     * @return UserUc
     */
    private function save(array $post)
    {
        try {
            $uuc_class = new UserUc();
            $user = User::find("*", ["email" => $post['user']]);
            $uc = Uc::find("*", ["code" => $post['uc']]);
            if (!$user || !$uc) {
                throw new Exception("Params receive not found", 422);
            }
            $uuc_class->iduc = $uc[0]->iduc;
            $uuc_class->iduser = $user[0]->iduser;
            if ($useruc_old = UserUc::find("uuc.*", ["uuc.iduc" => $uc[0]->iduc, "uuc.iduser" => $user[0]->iduser])) {
                if ($useruc_old[0]->status != 0) {
                    throw new Exception("User Already Register in Uc", 205);
                }
                $uuc_class->status = 2;
                if (!$uuc_class->updateStatus()) {
                    throw new Exception("Error on insert record", 422);
                }
            }
            if (!$uuc_class->insert()) {
                throw new Exception("Error on insert record", 422);
            }
        } catch (Exception $e) {
            Response::sendResponse($e->getCode(), ["msg" => $e->getMessage()]);
        }
        return $uuc_class;
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

<?php

// start
ini_set("display_errors", 1);
ini_set("log_errors", 1);
ini_set("error_log", "error.log");
require_once(__DIR__."/../../autoload.php");

use Libraries\MessageBroker;
use Models\UserUc;

function userucupdateStatusData($data)
{
    $model = new UserUc();
    $model->iduser = $data["iduser"];
    $model->iduc = $data["iduc"];
    $model->status = $data["status"];
    $model->updateStatus();
}

MessageBroker::processMessage("userucupdateStatus", "userucupdateStatusData");
exit();

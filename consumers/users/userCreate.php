<?php

// start
ini_set("display_errors", 1);
ini_set("log_errors", 1);
ini_set("error_log", "error.log");
require_once(__DIR__."/../../autoload.php");

use Libraries\MessageBroker;
use Models\User;

function userCreateData($data)
{
    $model = new User();
    $model->iduser = $data["iduser"];
    $model->name = $data["name"];
    $model->email = $data["email"];
    $model->insert();
}

MessageBroker::processMessage("userCreate", "userCreateData");
exit();

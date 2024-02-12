<?php

// start
ini_set("display_errors", 1);
ini_set("log_errors", 1);
ini_set("error_log", "error.log");
require_once(__DIR__."/../../autoload.php");

use Libraries\MessageBroker;
use Models\Uc;

function ucUpdateData($data)
{
    $model = new Uc();
    $model->iduc = $data["iduc"];
    $model->code = $data["code"];
    $model->name = $data["name"];
    $model->update();
}

MessageBroker::processMessage("ucUpdate", "ucUpdateData");
exit();
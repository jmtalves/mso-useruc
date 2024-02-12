<?php

// start
ini_set("display_errors", 1);
ini_set("log_errors", 1);
ini_set("error_log", "error.log");
require_once(__DIR__."/../../autoload.php");

use Libraries\MessageBroker;
use Models\Uc;

function ucDeleteData($data)
{
    $model = new Uc();
    $model->iduc = $data["iduc"];
    $model->delete();
}

MessageBroker::processMessage("ucDelete", "ucDeleteData");
exit();
<?php
/**
 * @package deployment
 */
require_once (__DIR__ . '/../../bootstrap.php');

$script = realpath(dirname(__FILE__) . "/../../../tests/standAloneClient/exec.php");
$newTemplateUpdate = realpath(dirname(__FILE__) . "/../../updates/scripts/xml/notifications/2022_07_27_add_kafka_user_events.xml");

print_r($newTemplateUpdate);
if(!file_exists($newTemplateUpdate) || !file_exists($script))
{
    KalturaLog::err("Missing update script file");
    return;
}

passthru("php $script $newTemplateUpdate");

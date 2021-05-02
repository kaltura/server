<?php
/**
 * @package deployment
 */
require_once (__DIR__ . '/../../bootstrap.php');

$script = realpath(dirname(__FILE__) . "/../../../tests/standAloneClient/exec.php");

$newTemplateUpdate = realpath(dirname(__FILE__) . "/../../updates/scripts/xml/notifications/2021_04_27_email_event_notification_entry_status_equal.xml");

if(!file_exists($newTemplateUpdate) || !file_exists($script))
{
    KalturaLog::err("Missing update script file");
    return;
}

passthru("php $script $newTemplateUpdate");

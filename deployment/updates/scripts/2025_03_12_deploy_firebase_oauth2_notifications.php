<?php
/**
 * @package deployment
 */
require_once (__DIR__ . '/../../bootstrap.php');

$script = realpath(dirname(__FILE__) . "/../../../tests/standAloneClient/exec.php");

$newTemplate = realpath(dirname(__FILE__) . "/../../updates/scripts/xml/notifications/2025_03_12_firebase_oauth2_notifications.xml");

if (!file_exists($newTemplate) || !file_exists($script))
{
	KalturaLog::err("Missing update script file");
	return;
}

passthru("php $script $newTemplate");

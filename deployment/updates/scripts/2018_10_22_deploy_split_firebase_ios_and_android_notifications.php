<?php
/**
 * @package deployment
 */
require_once (__DIR__ . '/../../bootstrap.php');

$script = realpath(dirname(__FILE__) . "/../../../tests/standAloneClient/exec.php");

$newFirebaseNotifications = realpath(dirname(__FILE__) . "/../../updates/scripts/xml/notifications/2018_10_22_split_firebase_ios_and_android_notifications.xml");

if(!file_exists($newFirebaseNotifications) || !file_exists($script))
{
	KalturaLog::err("Missing update script file");
	return;
}

passthru("php $script $newFirebaseNotifications");

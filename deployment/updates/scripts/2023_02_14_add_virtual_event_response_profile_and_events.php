<?php
/**
 * @package deployment
 */
require_once (__DIR__ . '/../../bootstrap.php');

$script = realpath(dirname(__FILE__) . '/../../../') . '/tests/standAloneClient/exec.php';
$veResponseProfile = realpath(dirname(__FILE__)) . '/xml/responseProfiles/2023_02_14_add_virtual_event_response_profiles.xml';
$veNotifications = realpath(dirname(__FILE__) . "/../../updates/scripts/xml/notifications/2023_02_13_add_kafka_virtual_event_events.xml");
$kuserNotificationUpdate = realpath(dirname(__FILE__) . "/../../updates/scripts/xml/notifications/2023_03_08_update_kafka_kuser_notifications.xml");

if(!file_exists($veResponseProfile) || !file_exists($veNotifications) || !file_exists($kuserNotificationUpdate))
{
	KalturaLog::err("Missing update script file");
	return;
}

passthru("php $script $veResponseProfile");
passthru("php $script $veNotifications");
passthru("php $script $kuserNotificationUpdate");

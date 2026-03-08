<?php
/**
 * @package deployment
 */
require_once (__DIR__ . '/../../bootstrap.php');

$script = realpath(dirname(__FILE__) . '/../../../') . '/tests/standAloneClient/exec.php';
$scheduleEventNotificationUpdate = realpath(dirname(__FILE__) . "/../../updates/scripts/xml/notifications/2025_06_16_add_kafka_schedule_event_events.xml");


if(!file_exists($scheduleEventNotificationUpdate))
{
	KalturaLog::err("Missing update script file");
	return;
}

passthru("php $script $scheduleEventNotificationUpdate");

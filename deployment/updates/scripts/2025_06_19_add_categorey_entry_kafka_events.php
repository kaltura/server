<?php
/**
 * @package deployment
 */
require_once (__DIR__ . '/../../bootstrap.php');

$script = realpath(dirname(__FILE__) . '/../../../') . '/tests/standAloneClient/exec.php';
$scheduleEventNotificationUpdate = realpath(dirname(__FILE__) . "/../../updates/scripts/xml/notifications/2025_06_19_add_kafka_category_entry_events.xml");

if(!file_exists($scheduleEventNotificationUpdate))
{
	KalturaLog::err("Missing update script file");
	return;
}

passthru("php $script $scheduleEventNotificationUpdate");

<?php
/**
 * @package deployment
 */
require_once (__DIR__ . '/../../bootstrap.php');

$script = realpath(dirname(__FILE__) . "/../../../tests/standAloneClient/exec.php");
$entryUpdate = realpath(dirname(__FILE__) . "/../../updates/scripts/xml/notifications/2025_05_07_add_kafka_entry_updated_notifications.xml");
$userDelete = realpath(dirname(__FILE__) . "/../../updates/scripts/xml/notifications/2025_05_07_update_kafka_kuser_notifications.xml");
$groupUserAddUpdate = realpath(dirname(__FILE__) . "/../../updates/scripts/xml/notifications/2025_05_07_add_kafka_groupuser_added_notifications.xml");



if(!file_exists($entryUpdate) ||
	!file_exists($userDelete) ||
	!file_exists($groupUserAddUpdate) ||
	!file_exists($script))
{
	KalturaLog::err("Missing script file");
	return;
}

if (!kConf::hasMap(kConfMapNames::KAFKA)){
	KalturaLog::err("Kafka configuration file (kafka.ini) wasn't found!");
	return;
}

$kafkaConfig = kConf::getMap(kConfMapNames::KAFKA);

if (!isset($kafkaConfig['brokers']) && !(isset($kafkaConfig['host']) && isset($kafkaConfig['port'])))
{
	KalturaLog::err("No Kafka brokers configured");
	return;
}

passthru("php $script $entryUpdate");
passthru("php $script $userDelete");
passthru("php $script $groupUserAddUpdate");




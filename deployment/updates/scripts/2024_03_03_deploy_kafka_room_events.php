<?php
/**
 * @package deployment
 */
require_once (__DIR__ . '/../../bootstrap.php');

$script = realpath(dirname(__FILE__) . "/../../../tests/standAloneClient/exec.php");
$newTemplateUpdate = realpath(dirname(__FILE__) . "/../../updates/scripts/xml/notifications/2024_03_03_add_kafka_room_entry_notifications.xml");

if(!file_exists($newTemplateUpdate) || !file_exists($script))
{
	KalturaLog::err("Missing update script file");
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

passthru("php $script $newTemplateUpdate");

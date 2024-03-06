<?php

/**
 * @package deployment
 */
require_once(__DIR__ . '/../../bootstrap.php');

$script = realpath(dirname(__FILE__) . "/../../../tests/standAloneClient/exec.php");

$newTemplateUpdate = realpath(dirname(__FILE__) . "/../../updates/scripts/xml/notifications/2024_01_18_batchJobEmailNotificationFailure.template.xml");

if (!file_exists($newTemplateUpdate) || !file_exists($script)) {
	KalturaLog::err("Missing update script file");
	return;
}

passthru("php $script $newTemplateUpdate");



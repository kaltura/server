<?php
/**
 * @package deployment
 */
require_once (__DIR__ . '/../../bootstrap.php');

$script = realpath(dirname(__FILE__) . "/../../../tests/standAloneClient/exec.php");

$taskApprovedModeration = realpath(dirname(__FILE__) . "/../../updates/scripts/xml/2018_02_22_entry_vendor_task_approved_moderation.xml");
deployTemplate($script, $taskApprovedModeration);

$taskPendingModeration = realpath(dirname(__FILE__) . "/../../updates/scripts/xml/2018_02_22_entry_vendor_task_pending_moderation.xml");
deployTemplate($script, $taskPendingModeration);

$taskRejectedModeration = realpath(dirname(__FILE__) . "/../../updates/scripts/xml/2018_02_22_entry_vendor_task_rejected_moderation.xml");
deployTemplate($script, $taskRejectedModeration);

$taskDone = realpath(dirname(__FILE__) . "/../../updates/scripts/xml/2018_02_22_entry_vendor_task_done.xml");
deployTemplate($script, $taskDone);

$creditReached75Percent = realpath(dirname(__FILE__) . "/../../updates/scripts/xml/2018_02_22_reach_credit_usage_over_75_percent.xml");
deployTemplate($script, $taskDone);

$creditReached90Percent = realpath(dirname(__FILE__) . "/../../updates/scripts/xml/2018_02_22_reach_credit_usage_over_90_percent.xml");
deployTemplate($script, $taskDone);

$creditReached100Percent = realpath(dirname(__FILE__) . "/../../updates/scripts/xml/2018_02_22_reach_credit_usage_over_100_percent.xml");
deployTemplate($script, $taskDone);

function deployTemplate($script, $config)
{
	if(!file_exists($config))
	{
		KalturaLog::err("Missing file [$config] will not deploy");
		return;
	}
	
	passthru("php $script $config");
}
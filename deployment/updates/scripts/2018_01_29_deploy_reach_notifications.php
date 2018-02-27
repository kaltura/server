<?php
/**
 * @package deployment
 */
require_once (__DIR__ . '/../../bootstrap.php');

$script = realpath(dirname(__FILE__) . "/../../../tests/standAloneClient/exec.php");

$taskApprovedModeration = realpath(dirname(__FILE__) . "/../../updates/scripts/xml/2018_02_22_entry_vendor_task_approved_moderation.template.xml");
deployTemplate($script, $taskApprovedModeration);

$taskPendingModeration = realpath(dirname(__FILE__) . "/../../updates/scripts/xml/2018_02_22_entry_vendor_task_pending_moderation.template.xml");
deployTemplate($script, $taskPendingModeration);

$taskRejectedModeration = realpath(dirname(__FILE__) . "/../../updates/scripts/xml/2018_02_22_entry_vendor_task_rejected_moderation.template.xml");
deployTemplate($script, $taskRejectedModeration);

$taskDone = realpath(dirname(__FILE__) . "/../../updates/scripts/xml/2018_02_22_entry_vendor_task_done.template.xml");
deployTemplate($script, $taskDone);

$creditReached75Precent = realpath(dirname(__FILE__) . "/../../updates/scripts/xml/2018_02_22_reach_credit_usage_over_75_percent.template.xml");
deployTemplate($script, $taskDone);

$taskDone = realpath(dirname(__FILE__) . "/../../updates/scripts/xml/2018_02_22_reach_credit_usage_over_90_percent.template.xml");
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
<?php
/**
 * @package deployment
 */
require_once (__DIR__ . '/../../bootstrap.php');

checkMandatoryPluginsEnabled();
$script = realpath(dirname(__FILE__) . "/../../../tests/standAloneClient/exec.php");

$taskApprovedModeration = realpath(dirname(__FILE__) . "/../../updates/scripts/xml/notifications/2018_02_22_entry_vendor_task_approved_moderation.xml");
deployTemplate($script, $taskApprovedModeration);

$taskPendingModeration = realpath(dirname(__FILE__) . "/../../updates/scripts/xml/notifications/2018_02_22_entry_vendor_task_pending_moderation.xml");
deployTemplate($script, $taskPendingModeration);

$taskRejectedModeration = realpath(dirname(__FILE__) . "/../../updates/scripts/xml/notifications/2018_02_22_entry_vendor_task_rejected_moderation.xml");
deployTemplate($script, $taskRejectedModeration);

$taskDone = realpath(dirname(__FILE__) . "/../../updates/scripts/xml/notifications/2018_02_22_entry_vendor_task_done.xml");
deployTemplate($script, $taskDone);

$creditReached75Percent = realpath(dirname(__FILE__) . "/../../updates/scripts/xml/notifications/2018_02_22_reach_credit_usage_over_75_percent.xml");
deployTemplate($script, $creditReached75Percent);

$creditReached90Percent = realpath(dirname(__FILE__) . "/../../updates/scripts/xml/notifications/2018_02_22_reach_credit_usage_over_90_percent.xml");
deployTemplate($script, $creditReached90Percent);

$creditReached100Percent = realpath(dirname(__FILE__) . "/../../updates/scripts/xml/notifications/2018_02_22_reach_credit_usage_over_100_percent.xml");
deployTemplate($script, $creditReached100Percent);

function deployTemplate($script, $config)
{
	if(!file_exists($config))
	{
		KalturaLog::err("Missing file [$config] will not deploy");
		return;
	}
	
	passthru("php $script $config");
}


/**
 * @return bool If all required plugins are installed
 */
function checkMandatoryPluginsEnabled()
{
	$pluginsFilePath = realpath(dirname(__FILE__) . "/../../../configurations/plugins.ini");
	KalturaLog::debug("Loading Plugins config from [$pluginsFilePath]");


	$pluginsData = file($pluginsFilePath);
	foreach ($pluginsData as $item)
	{
		if (trim($item) == "Reach")
			return;
	}
	KalturaLog::debug("[Reach] plugin is disabled or not configured, aborting execution");
	exit(-2);
}
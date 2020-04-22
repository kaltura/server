<?php
/**
 * @package deployment
 */
require_once (__DIR__ . '/../../bootstrap.php');

checkMandatoryPluginsEnabled();
$script = realpath(dirname(__FILE__) . "/../../../tests/standAloneClient/exec.php");

$taskApprovedExecution = realpath(dirname(__FILE__) . "/../../updates/scripts/xml/notifications/2019_07_24_update_task_approved_execution.xml");
deployTemplate($script, $taskApprovedExecution);

$taskFinishedProcessing = realpath(dirname(__FILE__) . "/../../updates/scripts/xml/notifications/2019_07_24_update_task_finished_processing.xml");
deployTemplate($script, $taskFinishedProcessing);

$taskRejectedExecution = realpath(dirname(__FILE__) . "/../../updates/scripts/xml/notifications/2019_07_24_update_task_rejected_for_execution.xml");
deployTemplate($script, $taskRejectedExecution);

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
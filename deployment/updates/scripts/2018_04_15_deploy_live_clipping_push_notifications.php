<?php

require_once(__DIR__ . "/../../../alpha/scripts/bootstrap.php");

checkMandatoryPluginsEnabled();
deployExplicitLivePushNotifications();

function deployExplicitLivePushNotifications()
{
	$script = realpath(dirname(__FILE__) . "/../../../tests/standAloneClient/exec.php");

	$liveClipping = realpath(dirname(__FILE__) . "/../../updates/scripts/xml/notifications/live_clipping_notification.xml");

	if (!file_exists($liveClipping))
	{
		KalturaLog::err("Missing notification file for deployign notifications");
		return;
	}

	passthru("php $script $liveClipping");
}

/**
 * Check if all plugins needed for live clipping to work are installed
 * @return bool If all required plugins are installed
 */
function checkMandatoryPluginsEnabled()
{
	$requiredPlugins = array("PushNotification", "Queue", "RabbitMQ");
	$pluginsFilePath = realpath(dirname(__FILE__) . "/../../../configurations/plugins.ini");
	KalturaLog::debug("Loading Plugins config from [$pluginsFilePath]");
	
	$pluginsData = file_get_contents($pluginsFilePath);
	foreach ($requiredPlugins as $requiredPlugin)
	{
		//check if plugin exists in file but is disabled
		if(strpos($pluginsData, ";".$requiredPlugin) !== false)
		{
			KalturaLog::debug("[$requiredPlugin] is disabled, aborting execution");
			exit(-2);
		}
		
		if(strpos($pluginsData, $requiredPlugin) === false)
		{
			KalturaLog::debug("[$requiredPlugin] not found in plugins data, aborting execution");
			exit(-2);
		}
	}
}

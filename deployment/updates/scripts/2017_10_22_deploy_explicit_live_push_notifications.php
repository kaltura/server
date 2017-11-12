<?php

require_once(__DIR__ . "/../../../alpha/scripts/bootstrap.php");

checkMandatoryPluginsEnabled();
deployExplicitLivePushNotifications();

function deployExplicitLivePushNotifications()
{
	$script = realpath(dirname(__FILE__) . "/../../../tests/standAloneClient/exec.php");

	$explicitLive = realpath(dirname(__FILE__) . "/../../updates/scripts/xml/notifications/explicit_live_notification.xml");

	if (!file_exists($explicitLive))
	{
		KalturaLog::err("Missing notification file for deployign notifications");
		return;
	}

	passthru("php $script $explicitLive");
}

/**
 * install required plugisn needed fot webcast to work
 * @return bool If plugis file updated or not
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

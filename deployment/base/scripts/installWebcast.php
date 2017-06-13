<?php

checkMandatoryPluginsEnabled();
deployWebcastPushNotifications();

function deployWebcastPushNotifications()
{
	$script = realpath(dirname(__FILE__) . "/../../../tests/standAloneClient/exec.php");
	
	$codeQnaConfig = realpath(dirname(__FILE__) . "/../../updates/scripts/xml/notifications/code_qna_notification.xml");
	$publicQnaConfig = realpath(dirname(__FILE__) . "/../../updates/scripts/xml/notifications/public_qna_notification.xml");
	$userQnaConfig = realpath(dirname(__FILE__) . "/../../updates/scripts/xml/notifications/user_qna_notification.xml");
	$pollsConfig = realpath(dirname(__FILE__) . "/../../updates/scripts/xml/notifications/polls_qna_notification.xml");
	
	if(!file_exists($codeQnaConfig) || !file_exists($publicQnaConfig) || !file_exists($userQnaConfig) || !file_exists($pollsConfig))
	{
		KalturaLog::err("Missing notification file for deployign notifications");
		return;
	}
	
	passthru("php $script $codeQnaConfig");	
	passthru("php $script $publicQnaConfig");
	passthru("php $script $userQnaConfig");
	passthru("php $script $pollsConfig");
}

/**
 * install required plugisn needed fot webcast to work
 * @return bool If plugis file updated or not
 */
function checkMandatoryPluginsEnabled()
{
	$requiredPlugins = array("PushNotification", "Queue", "RabbitMQ");
	$pluginsFilePath = realpath(dirname(__FILE__) . "/../../../configurations/plugins.ini");
	echo ("Loading Plugins config from [$pluginsFilePath]" . PHP_EOL);
	
	$pluginsData = file_get_contents($pluginsFilePath);
	foreach ($requiredPlugins as $requiredPlugin)
	{
		//check if plugin exists in file but is disabled
		if(strpos($pluginsData, ";".$requiredPlugin) !== false)
		{
			echo ("[$requiredPlugin] is disabled, aborting execution" . PHP_EOL);
			exit(-2);
		}
		
		if(strpos($pluginsData, $requiredPlugin) === false)
		{
			echo ("[$requiredPlugin] not found in plugins data, aborting execution" . PHP_EOL);
			exit(-2);
		}
	}
}

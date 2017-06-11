<?php

require_once(dirname(__FILE__) . '/../../bootstrap.php');
myDbHelper::$use_alternative_con = myDbHelper::DB_HELPER_CONN_MASTER;

$pluginsUpdated = installRequirePlugins();
if($pluginsUpdated)
{
	installPlugins();
	generateClients();
	generateFilters();
}

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
	
	passthru("php $script $config");	
	passthru("php $script $config");
	passthru("php $script $config");
	passthru("php $script $config");
}

function generateFilters()
{
	debug("Generating updated filters");
	$script = realpath(dirname(__FILE__) . "/../../../api_v3/generator/generate_filters.php");
	passthru("php $script");
	KalturaLog::debug("Done Generating updated filters");
}

function generateClients()
{
	KalturaLog::debug("Generating updated clientLibs");
	$script = realpath(dirname(__FILE__) . "/../../../generator/generate.php");
	passthru("php $script");
	KalturaLog::debug("Done Generating updated clientLibs");
}

function installPlugins()
{
	KalturaLog::debug("Running Install plugins");
	$script = realpath(dirname(__FILE__) . "/../scripts/installPlugins.php");
	passthru("php $script");
	KalturaLog::debug("Done Installing new plugins");
}

/**
 * install required plugisn needed fot webcast to work
 * @return bool If plugis file updated or not
 */
function installRequirePlugins()
{
	$requiredPlugins = array("PushNotification", "Queue", "RabbitMQ");
	$pluginsFileUpdated = false;
	
	$pluginsFilePath = realpath(dirname(__FILE__) . "/../../../configurations/plugins.ini");
	KalturaLog::debug("Loading Plugins config from [$pluginsFilePath]");
	
	$pluginsData = file_get_contents($pluginsFilePath);
	echo "pluginsData = " . print_r($pluginsData, true) . "\n"; 
	foreach ($requiredPlugins as $requiredPlugin)
	{
		//check if plugin exists in file but is disabled
		if(strpos($pluginsData, ";".$requiredPlugin) !== false)
		{
			KalturaLog::debug("[$requiredPlugin] disabled enabling it");
			$pluginsData = str_replace(";".$requiredPlugin, $requiredPlugin, $pluginsData);
			$pluginsFileUpdated = true;
			continue;
		}
		
		//check if plugin already enbaled
		if(strpos($pluginsData, $requiredPlugin) !== false)
		{
			KalturaLog::debug("[$requiredPlugin] already enabled");
			continue;
		}
		
		//Plugin does not exist in file add it
		KalturaLog::debug("[$requiredPlugin] not foudn in plugins file, adding it");
		$pluginsData .= "\n$requiredPlugin\n";
		$pluginsFileUpdated = true;
	}
	
	if($pluginsFileUpdated)
		file_put_contents($pluginsFilePath, $pluginsData);
	
	return $pluginsFileUpdated;
}

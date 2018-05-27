<?php
/**
 * @package deployment
 *
 * Deploy webcast defualt profiles & temlates
 *
 * No need to re-run after server code deploy
 */
require_once (__DIR__ . '/../../bootstrap.php');
checkMandatoryPluginsEnabled();

$script = realpath(dirname(__FILE__) . '/../../../') . '/tests/standAloneClient/exec.php';

$config = realpath(dirname(__FILE__)) . '/../../updates/scripts/xml/responseProfiles/reach_vendor_response_profiles.xml';
if(!file_exists($config))
	KalturaLog::err("Missing file [$config] will not deploy");

passthru("php $script $config");


/**
 * @return bool If all required plugins are installed
 */
function checkMandatoryPluginsEnabled()
{
	$requiredPlugins = array("Reach");
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

<?php
/**
 * @package deployment
 */
require_once (__DIR__ . '/../../bootstrap.php');

checkMandatoryPluginsEnabled();
$script = realpath(dirname(__FILE__) . "/../../../tests/standAloneClient/exec.php");

$creditExpired = realpath(dirname(__FILE__) . "/../../updates/scripts/xml/notifications/2019_07_23_update_reach_credit_expired.xml");
deployTemplate($script, $creditExpired);

$creditUsageOver75 = realpath(dirname(__FILE__) . "/../../updates/scripts/xml/notifications/2019_07_23_update_reach_credit_usage_over_75_percent.xml");
deployTemplate($script, $creditUsageOver75);

$creditUsageOver90 = realpath(dirname(__FILE__) . "/../../updates/scripts/xml/notifications/2019_07_23_update_reach_credit_usage_over_90_percent.xml");
deployTemplate($script, $creditUsageOver90);

$creditUsageOver100 = realpath(dirname(__FILE__) . "/../../updates/scripts/xml/notifications/2019_07_23_update_reach_credit_usage_over_100_percent.xml");
deployTemplate($script, $creditUsageOver100);

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
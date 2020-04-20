<?php
/**
 * @package deployment
 */
require_once (__DIR__ . '/../../bootstrap.php');

$script = realpath(dirname(__FILE__) . "/../../../tests/standAloneClient/exec.php");

$xml = realpath(dirname(__FILE__) . "/../../updates/scripts/xml/2020_03_30_User_Deleted_A_Comment.template.xml");
deployTemplate($script, $xml);

$xml = realpath(dirname(__FILE__) . "/../../updates/scripts/xml/2020_03_30_User_Deleted_A_Comment_AppSpecific.template.xml");
deployTemplate($script, $xml);

$xml = realpath(dirname(__FILE__) . "/../../updates/scripts/xml/2020_03_30_User_Replied_To_Comment.template.xml");
deployTemplate($script, $xml);

$xml = realpath(dirname(__FILE__) . "/../../updates/scripts/xml/2020_03_30_User_Replied_To_Comment_AppSpecific.template.xml");
deployTemplate($script, $xml);

function deployTemplate($script, $config)
{
	if(!file_exists($config))
	{
		KalturaLog::err("Missing file [$config] will not deploy");
		return;
	}

	passthru("php $script $config");
}

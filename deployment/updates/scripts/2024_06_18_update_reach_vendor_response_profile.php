<?php
/**
 * @package deployment
 */
require_once (__DIR__ . '/../../bootstrap.php');

$script = realpath(dirname(__FILE__) . '/../../../') . '/tests/standAloneClient/exec.php';
$config = realpath(dirname(__FILE__)) . '/xml/responseProfiles/2024_06_18_update_reach_vendor_response_profiles.xml';

if(!file_exists($config))
{
	KalturaLog::err("Missing update script file");
	return;
}

passthru("php $script $config");
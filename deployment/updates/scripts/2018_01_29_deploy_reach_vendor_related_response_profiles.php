<?php
/**
 * @package deployment
 *
 * Deploy webcast defualt profiles & temlates
 *
 * No need to re-run after server code deploy
 */
require_once (__DIR__ . '/../../bootstrap.php');
$script = realpath(dirname(__FILE__) . '/../../../') . '/tests/standAloneClient/exec.php';

$config = realpath(dirname(__FILE__)) . '/../../updates/scripts/xml/responseProfiles/reach_vendor_response_profiles.xml';
if(!file_exists($config))
	KalturaLog::err("Missing file [$config] will not deploy");

passthru("php $script $config");

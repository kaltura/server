<?php
/**
 * @package deployment
 * @subpackage live.liveStream
 *
 * Create live packager delivery profiles
 *
 */
require_once (__DIR__ . '/../../bootstrap.php');

$script = realpath(dirname(__FILE__) . '/../../') . '/base/scripts/insertDefaults.php';

$configHls = realpath(dirname(__FILE__) . '/../../') . '/base/scripts/init_data/07.DeliveryProfileLivePackagerHls.ini';
$configHds = realpath(dirname(__FILE__) . '/../../') . '/base/scripts/init_data/07.DeliveryProfileLivePackagerHds.ini';
$configDash = realpath(dirname(__FILE__) . '/../../') . '/base/scripts/init_data/07.DeliveryProfileLivePackagerDash.ini';
$configMss = realpath(dirname(__FILE__) . '/../../') . '/base/scripts/init_data/07.DeliveryProfileLivePackagerMss.ini';

if(!file_exists($configHls) || !file_exists($configHds) || !file_exists($configDash) || !file_exists($configMss))
{
	KalturaLog::err("Missing delivery profile configuration files for deploying live packager delivery porfiles");
	exit(-2);
}

passthru("php $script $configHls");
passthru("php $script $configHds");
passthru("php $script $configDash");
passthru("php $script $configMss");

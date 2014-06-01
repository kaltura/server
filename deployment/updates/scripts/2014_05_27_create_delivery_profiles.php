<?php
/**
 * @package deployment
 * @subpackage live.liveStream
 *
 * Create live selivery profiles
 *
 * No need to re-run after server code deploy
 */
$script = realpath(dirname(__FILE__) . '/../../') . '/base/scripts/insertDefaults.php';

$config = realpath(dirname(__FILE__) . '/../../') . '/base/scripts/init_data/07.DeliveryProfileLiveAppleHttp.ini';
passthru("php $script $config");

$config = realpath(dirname(__FILE__) . '/../../') . '/base/scripts/init_data/07.DeliveryProfileLiveHds.ini';
passthru("php $script $config");

$config = realpath(dirname(__FILE__) . '/../../') . '/base/scripts/init_data/07.DeliveryProfileLiveRtmp.ini';
passthru("php $script $config");


$config = realpath(dirname(__FILE__) . '/../../') . '/base/scripts/init_data/07.DeliveryProfileAkamaiHttp.ini';
passthru("php $script $config");

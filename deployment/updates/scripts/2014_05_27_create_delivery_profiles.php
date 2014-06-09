<?php
/**
 * @package deployment
 * @subpackage live.liveStream
 *
 * Create live selivery profiles
 *
 * No need to re-run after server code deploy
 */
require_once (__DIR__ . '/../../bootstrap.php');

$script = realpath(dirname(__FILE__) . '/../../') . '/base/scripts/insertDefaults.php';
$cdnApiHost = kConf::get("cdn_api_host");

$config = realpath(dirname(__FILE__) . '/../../') . '/base/scripts/init_data/07.DeliveryProfileLiveAppleHttp.ini';
passthru("php $script $config");

$config = realpath(dirname(__FILE__) . '/../../') . '/base/scripts/init_data/07.DeliveryProfileLiveHds.ini';
passthru("php $script $config");

$config = realpath(dirname(__FILE__) . '/../../') . '/base/scripts/init_data/07.DeliveryProfileLiveRtmp.ini';
passthru("php $script $config");


$configTemplate = realpath(dirname(__FILE__) . '/../../') . '/base/scripts/init_data/07.DeliveryProfileAkamaiHttp.template.ini';
$config = realpath(dirname(__FILE__) . '/../../') . '/base/scripts/init_data/07.DeliveryProfileAkamaiHttp.ini';
$content = file_get_contents($configTemplate);
$content = str_replace("@HOST_NAME@", $cdnApiHost, $content);
file_put_contents($config, $content);
passthru("php $script $config");
unlink($config);


$configTemplate = realpath(dirname(__FILE__) . '/../../') . '/base/scripts/init_data/07.DeliveryProfileAkamaiAppleHttp.template.ini';
$config = realpath(dirname(__FILE__) . '/../../') . '/base/scripts/init_data/07.DeliveryProfileAkamaiAppleHttp.ini';
$content = file_get_contents($configTemplate);
$content = str_replace("@HOST_NAME@", $cdnApiHost, $content);
file_put_contents($config, $content);
passthru("php $script $config");
unlink($config);


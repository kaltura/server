<?php
/**
 * @package deployment
 * @subpackage live.liveStream
 *
 * Create live dash delivery profile
 *
 * No need to re-run after server code deploy
 */
require_once (__DIR__ . '/../../bootstrap.php');

$script = realpath(dirname(__FILE__) . '/../../') . '/base/scripts/insertDefaults.php';
$config = realpath(dirname(__FILE__) . '/../../') . '/base/scripts/init_data/07.DeliveryProfileLiveDash.ini';
passthru("php $script $config");

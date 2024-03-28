<?php
/**
 * @package deployment
 *
 * Deploy new UHD flavors for live
 */
require_once (__DIR__ . '/../../bootstrap.php');
$script = realpath(dirname(__FILE__) . '/../../') . '/base/scripts/insertDefaults.php';

$config = realpath(dirname(__FILE__)) . '/ini_files/2024_03_21_live_1080.liveParams.ini';
passthru("php $script $config");

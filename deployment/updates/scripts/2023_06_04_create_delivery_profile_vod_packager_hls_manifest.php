<?php
/**
 * @package deployment
 *
 * Create VOD HLS manifest delivery profile - VOD_PACKAGER_HLS_MANIFEST
 *
 */
require_once (__DIR__ . '/../../bootstrap.php');

$script = realpath(dirname(__FILE__) . '/../../') . '/base/scripts/insertDefaults.php';
$config = realpath(dirname(__FILE__) . '/../../') . '/base/scripts/init_data/07.DeliveryProfileVodPackagerHlsManifest.ini';
passthru("php $script $config");

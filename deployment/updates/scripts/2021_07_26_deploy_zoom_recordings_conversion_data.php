<?php
/**
 * @package deployment
 *
 * Deploy zoom recordings Flavors + conversion Profile
 *
 * No need to re-run after server code deploy
 */
require_once (__DIR__ . '/../../bootstrap.php');
$script = realpath(dirname(__FILE__) . '/../../') . '/base/scripts/insertDefaults.php';

$config = realpath(dirname(__FILE__)) . '/ini_files/2021_07_26_zoom_recodrings.conversionProfile2.ini';
passthru("php $script $config");

$config = realpath(dirname(__FILE__)) . '/ini_files/2021_07_26_zoom_recordings.flavorParamsConversionProfile.ini';
passthru("php $script $config");
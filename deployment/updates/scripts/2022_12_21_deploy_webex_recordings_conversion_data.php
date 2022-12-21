<?php
/**
 * @package deployment
 *
 * Deploy Webex recordings Flavors + conversion Profile
 *
 * No need to re-run after server code deploy
 */
require_once (__DIR__ . '/../../bootstrap.php');
$script = realpath(dirname(__FILE__) . '/../../') . '/base/scripts/insertDefaults.php';

$config = realpath(dirname(__FILE__)) . '/ini_files/2022_12_21_webex_recordings.conversionProfile2.ini';
passthru("php $script $config");

$config = realpath(dirname(__FILE__)) . '/ini_files/2022_12_21_webex_recordings.flavorParamsConversionProfile.ini';
passthru("php $script $config");

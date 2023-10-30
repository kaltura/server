<?php
/**
 * @package deployment
 *
 * Deploy Kaltura meetings Flavors + conversion Profile
 *
 * No need to re-run after server code deploy
 */
require_once (__DIR__ . '/../../bootstrap.php');
$script = realpath(dirname(__FILE__) . '/../../') . '/base/scripts/insertDefaults.php';

$config = realpath(dirname(__FILE__)) . '/ini_files/2023_10_31_kaltura_meetings_documents.ImageFlavorParams.ini';
passthru("php $script $config");

$config = realpath(dirname(__FILE__)) . '/ini_files/2023_10_31_kaltura_meetings_documents.conversionProfile2.ini';
passthru("php $script $config");

$config = realpath(dirname(__FILE__)) . '/ini_files/2023_10_31_kaltura_meetings_documents.flavorParamsConversionProfile.ini';
passthru("php $script $config");
<?php
/**
 * @package deployment
 *
 * Deploy Lecture Capture Flavors + conversion Profile
 *
 * No need to re-run after server code deploy
 */
require_once (__DIR__ . '/../../bootstrap.php');
$script = realpath(dirname(__FILE__) . '/../../') . '/base/scripts/insertDefaults.php';

$config = realpath(dirname(__FILE__)) . '/ini_files/01_lecture_capture.flavorParams.ini';
passthru("php $script $config");

$config = realpath(dirname(__FILE__)) . '/ini_files/02_lecture_capture.conversionProfile2.ini';
passthru("php $script $config");

$config = realpath(dirname(__FILE__)) . '/ini_files/03_lecture_capture.flavorParamsConversionProfile.ini';
passthru("php $script $config");
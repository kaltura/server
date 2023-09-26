<?php
/**
 * @package deployment
 *
 * Deploy new Azerbaijani and Urdu language flavors for live
 */
require_once (__DIR__ . '/../../bootstrap.php');
$script = realpath(dirname(__FILE__) . '/../../') . '/base/scripts/insertDefaults.php';

$config = realpath(dirname(__FILE__)) . '/ini_files/2023_09_24_live_languages_aze_urd.liveParams.ini';
passthru("php $script $config");

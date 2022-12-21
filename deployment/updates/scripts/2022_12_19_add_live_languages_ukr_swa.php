<?php
/**
 * @package deployment
 *
 * Deploy new Ukrainian and Swahili language flavors for live
 *
 * No need to re-run after server code deploy
 */
require_once (__DIR__ . '/../../bootstrap.php');
$script = realpath(dirname(__FILE__) . '/../../') . '/base/scripts/insertDefaults.php';

$config = realpath(dirname(__FILE__)) . '/ini_files/2022_12_19_live_languages_swa_ukr.liveParams.ini';
passthru("php $script $config");

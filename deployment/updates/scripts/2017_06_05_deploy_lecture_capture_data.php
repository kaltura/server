<?php
/**
 * @package deployment
 *
 * Deploy live hd & language packages defualt live params
 *
 * No need to re-run after server code deploy
 */
require_once (__DIR__ . '/../../bootstrap.php');
$script = realpath(dirname(__FILE__) . '/../../') . '/base/scripts/insertDefaults.php';

$config = realpath(dirname(__FILE__)) . '/ini_files/live_language_package.liveParams.ini';
passthru("php $script $config");

$config = realpath(dirname(__FILE__)) . '/ini_files/live_hd_package.liveParams.ini';
passthru("php $script $config");

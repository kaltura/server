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

$config = realpath(dirname(__FILE__)) . '/ini_files/4k.flavorParams.ini';
passthru("php $script $config");

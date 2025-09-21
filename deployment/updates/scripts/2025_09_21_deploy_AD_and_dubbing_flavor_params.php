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

$config = realpath(dirname(__FILE__)) . '/ini_files/audio_description_flavor_params.flavorParams.ini';
passthru("php $script $config");

$config = realpath(dirname(__FILE__)) . '/ini_files/dubbing_flavor_params.flavorParams.ini';
passthru("php $script $config");
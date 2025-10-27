<?php
/**
 * @package deployment
 *
 * Deploy dubbing & standard audio description language packages
 *
 * No need to re-run after server code deploy
 */
require_once (__DIR__ . '/../../bootstrap.php');
$script = realpath(dirname(__FILE__) . '/../../') . '/base/scripts/insertDefaults.php';

$config = realpath(dirname(__FILE__)) . '/ini_files/audio_description_flavor_params.flavorParams.ini';
passthru("php $script $config");

$config = realpath(dirname(__FILE__)) . '/ini_files/dubbing_flavor_params.flavorParams.ini';
passthru("php $script $config");
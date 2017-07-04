<?php
/**
 * @package deployment
 * @subpackage live.liveStream
 *
 * Create live dash delivery profile
 *
 * No need to re-run after server code deploy
 */
//require_once (__DIR__ . '/../../bootstrap.php');

$script = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';
$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.cuepoint.cuepoint.ini';
passthru("php $script $config");

$script = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';
$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.thumbasset.ini';
passthru("php $script $config");

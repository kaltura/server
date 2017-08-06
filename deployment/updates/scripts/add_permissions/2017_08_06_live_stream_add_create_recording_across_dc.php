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

$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.livestream.ini';
passthru("php $script $config");

$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.livechannel.ini';
passthru("php $script $config");

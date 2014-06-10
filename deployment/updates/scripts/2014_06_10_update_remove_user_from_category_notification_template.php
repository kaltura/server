<?php
/**
 * @package deployment
 * @subpackage live.liveStream
 *
 * Create live selivery profiles
 *
 * No need to re-run after server code deploy
 */
require_once (__DIR__ . '/../../bootstrap.php');

$script = realpath(dirname(__FILE__) . '/../../../') . '/tests/standAloneClient/exec.php';
$config = realpath(dirname(__FILE__) . '/../../../') . '/tests/standAloneClient/updateRemoveUserFromCategoryEmailNotificationTemplate.xml';
passthru("php $script $config");


	
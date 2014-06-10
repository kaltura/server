<?php
/**
 * @package deployment
 *
 * update remove_user_from_category notification template to OBJECT_DELETED event_type
 *
 * No need to re-run after server code deploy
 */
require_once (__DIR__ . '/../../bootstrap.php');

$script = realpath(dirname(__FILE__) . '/../../../') . '/tests/standAloneClient/exec.php';
$config = realpath(dirname(__FILE__) . '/../../../') . '/tests/standAloneClient/updateRemoveUserFromCategoryEmailNotificationTemplate.xml';
passthru("php $script $config");


	
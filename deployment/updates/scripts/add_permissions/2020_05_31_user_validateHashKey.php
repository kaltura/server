<?php
/**
 * @package deployment
 * Add permissions to user validateHashKey
 */
//require_once (__DIR__ . '/../../bootstrap.php');

$script = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';

$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.user.ini';
passthru("php $script $config");


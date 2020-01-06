<?php
/**
 * @package deployment
 * @subpackage falcon.roles_and_permissions
 *
 * Add permissions to caption asset
 */

define('DEPLOYMENT_DIR', realpath(__DIR__ . '/../..'));
require_once (DEPLOYMENT_DIR . '/bootstrap.php');

$script = realpath(dirname(__FILE__) . '/../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';
$config = realpath(dirname(__FILE__)) . '/../../permissions/service.pushnotification.pushnotificationtemplate.ini';
passthru("php $script $config");

$config = realpath(dirname(__FILE__)) . '/../../permissions/service.livestream.ini';
passthru("php $script $config");
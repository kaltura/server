<?php
/**
 * @package deployment
 * @subpackage falcon.roles_and_permissions
 *
 * Adds category service permissions
 *
 * No need to re-run after server code deploy
 */

$script = realpath(dirname(__FILE__) . '/../../../../') . '/scripts/utils/permissions/addPermissionsAndItems.php';
$config = realpath(dirname(__FILE__)) . '/configs/always_allowed_internal_action_permission.ini';
passthru("php $script $config");

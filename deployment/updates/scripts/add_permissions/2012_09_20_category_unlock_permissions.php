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
$config = realpath(dirname(__FILE__)) . '/configs/category_unlock_permissions.ini';
passthru("php $script $config");

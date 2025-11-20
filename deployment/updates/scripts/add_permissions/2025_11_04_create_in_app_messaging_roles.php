<?php
/**
 * @package deployment
 * @subpackage Venus.roles_and_permissions
 *
 * Create In-App Messaging roles and permissions
 */

$insertDefaultsScript = realpath(dirname(__FILE__) . '/../../../') . '/base/scripts/insertDefaults.php';
$permissionConfig = realpath(dirname(__FILE__) . '/../../../') . '/base/scripts/init_data/02.Permission.ini';
$roleConfig = realpath(dirname(__FILE__)) . '/../../../base/scripts/init_data/03.UserRole.ini';

passthru("php $insertDefaultsScript $permissionConfig");
passthru("php $insertDefaultsScript $roleConfig");

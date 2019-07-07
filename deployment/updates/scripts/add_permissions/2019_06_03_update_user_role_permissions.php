<?php
/**
 * @package deployment
 * @subpackage orion.roles_and_permissions
 */

$addPermissionsScript = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/removePermissionsAndItems.php';

$removeConfig = realpath(dirname(__FILE__)) . '/../ini_files/2019_06_03_userrole_update_permissions.ini';
passthru("php $addPermissionsScript $removeConfig");


<?php
/**
 * @package deployment
 * update permissions to system getHealthCheck
 */

$removePermissionsScript = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/removePermissionsAndItems.php';
$removeConfig = realpath(dirname(__FILE__)) . '/../ini_files/2020_01_09_system_update_permissions.ini';
passthru("php $removePermissionsScript $removeConfig");

$addPermissionsScript = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';
$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.system.ini';
passthru("php $addPermissionsScript $config");
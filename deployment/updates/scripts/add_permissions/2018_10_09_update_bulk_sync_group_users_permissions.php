<?php
/**
 * @package deployment
 * @subpackage mercury.roles_and_permissions
 */
$removePermissionsScript = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/removePermissionsAndItems.php';
$removeConfig = realpath(dirname(__FILE__)) . '/../ini_files/2018_10_02_bulk_update_permissions.ini';
passthru("php $removePermissionsScript $removeConfig");


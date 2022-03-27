<?php
/**
 * @package deployment
 * @subpackage rigel.roles_and_permissions
 */

$removePermissionsScript = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/removePermissionsAndItems.php';
$removeConfig = realpath(dirname(__FILE__)) . '/../ini_files/2020_03_22_userscore_update_permissions.ini';
passthru("php $removePermissionsScript $removeConfig");

$script = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';
$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.game.userscore.ini';
passthru("php $script $config");
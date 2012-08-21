<?php
/**
 * @package deployment
 * @subpackage dragonfly.roles_and_permissions
 */

$script = realpath(dirname(__FILE__) . '/../../../../') . '/scripts/utils/permissions/addPermissionsAndItems.php';
$config = realpath(dirname(__FILE__)) . '/configs/user_getbyloginid_permission.ini';
passthru("php $script $config");

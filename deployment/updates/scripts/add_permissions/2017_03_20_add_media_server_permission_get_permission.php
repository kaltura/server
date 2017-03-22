<?php
/**
 * @package deployment
 * @subpackage lynx.roles_and_permissions
 */

$addPermissionsAndItemsScript = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';

$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.permission.ini';
passthru("php $addPermissionsAndItemsScript $config");

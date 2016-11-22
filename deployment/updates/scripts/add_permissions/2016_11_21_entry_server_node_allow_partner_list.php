<?php
/**
 * @package deployment
 * @subpackage Lynx.roles_and_permissions
 */

$addPermissionsAndItemsScript = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';
$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.entryservernode.ini';
passthru("php $addPermissionsAndItemsScript $config");

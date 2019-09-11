<?php
/**
 * @package deployment
 * @subpackage orion.roles_and_permissions
 */
$addPermissionsScript = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';
$addConfig = realpath(dirname(__FILE__)) . '/../../../permissions/service.user.ini';
passthru("php $addPermissionsScript $addConfig");
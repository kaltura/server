<?php
/**
 * @package deployment
 * @subpackage mercury.roles_and_permissions
 */

$addPermissionsScript = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';

$addConfig = realpath(dirname(__FILE__)) . '/../../../permissions/service.groupuser.ini';
passthru("php $addPermissionsScript $addConfig");

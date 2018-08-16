<?php
/**
 * @package deployment
 * @subpackage naos.roles_and_permissions
 */

$addPermissionsAndItemsScript = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';

$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.report.ini';
passthru("php $addPermissionsAndItemsScript $config");


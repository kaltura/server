<?php
/**
 * @package deployment
 * @subpackage scorpius.roles_and_permissions
 */

$addPermissionsAndItemsScript = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';

$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.vendor.vendorIntegration.ini';
passthru("php $addPermissionsAndItemsScript $config");

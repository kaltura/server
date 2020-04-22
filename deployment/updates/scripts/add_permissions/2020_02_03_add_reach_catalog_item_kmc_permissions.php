<?php
/**
 * @package deployment
 * @subpackage orion.roles_and_permissions
 */

$removePermissionsScript = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/removePermissionsAndItems.php';
$removeConfig = realpath(dirname(__FILE__)) . '/../ini_files/2020_02_04_object_reach_catalogitem_update_permissions.ini';
echo "Running php $removePermissionsScript $removeConfig";
passthru("php $removePermissionsScript $removeConfig");

$script = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';
$config = realpath(dirname(__FILE__)) . '/../../../permissions/object.KalturaVendorCatalogItem.ini';
echo "Running php $script $config";
passthru("php $script $config");


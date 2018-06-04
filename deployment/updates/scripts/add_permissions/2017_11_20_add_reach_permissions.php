<?php
/**
 * @package deployment
 * @subpackage mercury.roles_and_permissions
 */
$script = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';
//$script = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/removePermissionsAndItems.php';
$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.reach.vendorCatalogItem.ini';
echo "Running php $script $config";
passthru("php $script $config");

$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.reach.partnerCatalogItem.ini';
echo "Running php $script $config";
passthru("php $script $config");

$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.reach.reachProfile.ini';
echo "Running php $script $config";
passthru("php $script $config");


$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.reach.entryVendorTask.ini';
echo "Running php $script $config";
passthru("php $script $config");

/// adding objects permissions
$config = realpath(dirname(__FILE__)) . '/../../../permissions/object.KalturaReachProfile.ini';
echo "Running php $script $config";
passthru("php $script $config");

$config = realpath(dirname(__FILE__)) . '/../../../permissions/object.KalturaEntryVendorTask.ini';
echo "Running php $script $config";
passthru("php $script $config");

$config = realpath(dirname(__FILE__)) . '/../../../permissions/object.KalturaVendorCatalogItem.ini';
echo "Running php $script $config";
passthru("php $script $config");


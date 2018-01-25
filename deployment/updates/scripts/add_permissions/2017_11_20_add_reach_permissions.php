<?php
/**
 * @package deployment
 * @subpackage mercury.roles_and_permissions
 */
$script = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';

$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.reach.vendorCatalogItem.ini';
echo "Running php $script $config";
passthru("php $script $config");

$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.reach.partnerCatalogItem.ini';
echo "Running php $script $config";
passthru("php $script $config");

$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.reach.vendorProfile.ini';
echo "Running php $script $config";
passthru("php $script $config");


$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.reach.entryVendorTask.ini';
echo "Running php $script $config";
passthru("php $script $config");

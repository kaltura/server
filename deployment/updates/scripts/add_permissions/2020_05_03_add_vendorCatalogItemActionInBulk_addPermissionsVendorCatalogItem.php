<?php
/**
 * @package deployment
 * Add permissions to vendorCatalogItem and addBulkUpload action
 */

$addPermissionsAndItemsScript = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';

$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.reach.vendorCatalogItem.ini ';
passthru("php $addPermissionsAndItemsScript $config");
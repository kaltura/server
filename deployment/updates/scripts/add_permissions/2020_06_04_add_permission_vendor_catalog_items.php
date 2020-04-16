<?php
/**
 * @package deployment
 * Add permissions to reach vendor catalog items export and getexporturl
 */

$script = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';
$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.reach.vendorCatalogItem.ini';
passthru("php $script $config");
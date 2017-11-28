<?php
/**
 * @package deployment
 * @subpackage mercury.roles_and_permissions
 */
$script = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';
$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.vendorCatalogItem.vendorCatalogItem.ini';
passthru("php $script $config");

<?php
/**
 * @package deployment
 * @subpackage venus.roles_and_permissions
 */

$script = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';

$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.reach.vendorCatalogItem.ini';
passthru("php $script $config");

$addConfig = realpath(dirname(__FILE__)) . '/../../../permissions/service.session.ini';
passthru("php $script $addConfig");

<?php
/**
 * @package deployment
 * @subpackage kajam.roles_and_permissions
 */

$addPermissionsAndItemsScript = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';
$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.flavorasset.ini';
passthru("php $addPermissionsAndItemsScript $config");

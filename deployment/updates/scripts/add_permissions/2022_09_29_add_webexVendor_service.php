<?php
/**
 * @package deployment
 * @subpackage rigel.roles_and_permissions
 */

$addPermissionsAndItemsScript = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';

$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.WebexAPIDropFolder.webexVendor.ini';
passthru("php $addPermissionsAndItemsScript $config");

<?php
/**
 * @package deployment
 * @subpackage quasar.roles_and_permissions
 */

$addPermissionsAndItemsScript = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';

$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.fileasset.ini';
passthru("php $addPermissionsAndItemsScript $config");

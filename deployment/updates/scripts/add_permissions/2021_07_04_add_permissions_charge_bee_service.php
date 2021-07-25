<?php
/**
 * @package deployment
 * @subpackage Quasar.roles_and_permissions
 * 
 * Added permission to chargeBee new service
 */

$script = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';
$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.chargeBee.chargeBeeVendor.ini';
passthru("php $script $config");
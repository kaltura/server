<?php
/**
 * @package deployment
 * @subpackage quasar.roles_and_permissions
 * Add permission -11>PARTNER_-11_GROUP_*_PERMISSION to service: session action: impersonate
 * Add permission -11>PARTNER_-11_GROUP_*_PERMISSION to service: groupuser: action: list
 */

$script = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';
$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.session.ini';
passthru("php $script $config");

$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.groupuser.ini';
passthru("php $script $config");

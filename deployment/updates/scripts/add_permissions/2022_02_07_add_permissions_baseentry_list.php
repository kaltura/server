<?php
/**
 * @package deployment
 * @subpackage quasar.roles_and_permissions
 * Add permission -13>PARTNER_-13_GROUP_*_PERMISSION to service: baseentry action: list
 */

$script = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';
$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.baseentry.ini';
passthru("php $script $config");
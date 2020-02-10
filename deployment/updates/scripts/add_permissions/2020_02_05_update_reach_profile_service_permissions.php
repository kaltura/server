<?php
/**
 * @package deployment
 * @subpackage orion.roles_and_permissions
 */
$script = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';

$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.reach.reachProfile.ini';
echo "Running php $script $config";
passthru("php $script $config");

$config = realpath(dirname(__FILE__)) . '/../../../permissions/object.KalturaReachProfile.ini';
echo "Running php $script $config";
passthru("php $script $config");


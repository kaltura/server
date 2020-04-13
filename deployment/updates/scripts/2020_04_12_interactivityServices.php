<?php
/**
 * @package deployment
 * @subpackage propus.roles_and_permissions
 */

$script = realpath(dirname(__FILE__) . '/../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';

$config = realpath(dirname(__FILE__)) . '/../../permissions/service.interactivity.interactivity.ini';
echo "Running php $script $config\n";
passthru("php $script $config");


$config = realpath(dirname(__FILE__)) . '/../../permissions/service.interactivity.volatileInteractivity.ini';
echo "Running php $script $config\n";
passthru("php $script $config");
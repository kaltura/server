<?php
/**
 * @package deployment
 * @subpackage ix.roles_and_permissions
 */

$script = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';
$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.jobs.ini';
passthru("php $script $config");
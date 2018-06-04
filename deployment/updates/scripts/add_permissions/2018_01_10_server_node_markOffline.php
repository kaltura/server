<?php
/**
 * @package deployment
 * @subpackage falcon.roles_and_permissions
 * 
 * Add action of unregister to servernode
 */
$script = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';
$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.servernode.ini';
passthru("php $script $config");
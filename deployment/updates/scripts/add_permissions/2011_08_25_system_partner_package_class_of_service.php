<?php
/**
 * @package deployment
 * @subpackage dragonfly.roles_and_permissions
 * 
 * Adds drop folder permissions
 * 
 * No need to re-run after server code deploy
 */

$script = realpath(dirname(__FILE__) . '/../../../../') . '/scripts/utils/permissions/addPermissionsAndItems.php';
$config = realpath(dirname(__FILE__)) . '/configs/system_partner_package_class_of_service.ini';
passthru("php $script $config");
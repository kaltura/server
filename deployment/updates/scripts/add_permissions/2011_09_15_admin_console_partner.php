<?php
/**
 * @package deployment
 * @subpackage eagle.roles_and_permissions
 * 
 * Adds admin console partner permissions
 * 
 * No need to re-run after server code deploy
 */

$script = realpath(dirname(__FILE__) . '/../../../../') . '/scripts/utils/permissions/addPermissionsAndItems.php';
$config = realpath(dirname(__FILE__)) . '/configs/admin_console_partner_permissions.ini';
passthru("php $script $config");
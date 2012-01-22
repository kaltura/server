<?php
/**
 * @package deployment
 * @subpackage dragonfly.roles_and_permissions
 * 
 * 
 * No need to re-run after server code deploy
 */

$script = realpath(dirname(__FILE__) . '/../../../../') . '/scripts/utils/permissions/addPermissionsAndItems.php';
$config = realpath(dirname(__FILE__)) . '/configs/admin_console_reportadmin.ini';
passthru("php $script $config");
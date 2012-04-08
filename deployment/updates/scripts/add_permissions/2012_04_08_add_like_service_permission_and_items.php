<?php
/**
 * @package deployment
 * @subpackage dragonfly.roles_and_permissions
 * 
 * Adds like permissions
 * 
 * No need to re-run after server code deploy
 */

$script = realpath(dirname(__FILE__) . '/../../../../') . '/scripts/utils/permissions/addPermissionsAndItems.php';
$config = realpath(dirname(__FILE__)) . '/configs/like_permission_items.ini';
passthru("php $script $config");
//exec("php $script $config");

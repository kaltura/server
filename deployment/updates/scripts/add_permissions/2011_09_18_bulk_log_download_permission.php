<?php
/**
 * @package deployment
 * @subpackage dragonfly.roles_and_permissions
 * 
 * Adds permissions for bulk upload emails ks
 * 
 * No need to re-run after server code deploy
 */

$script = realpath(dirname(__FILE__) . '/../../../../') . '/scripts/utils/permissions/addPermissionsAndItems.php';
$config = realpath(dirname(__FILE__)) . '/configs/bulk_log_download_permission.ini';
passthru("php $script $config");
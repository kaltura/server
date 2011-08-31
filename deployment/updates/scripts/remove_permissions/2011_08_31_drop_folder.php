<?php
/**
 * @package deployment
 * @subpackage eagle.roles_and_permissions
 * 
 * No need to re-run after server code deploy
 */

$script = realpath(dirname(__FILE__) . '/../../../../') . '/scripts/utils/permissions/removePermissionsAndItems.php';
$config = realpath(dirname(__FILE__)) . '/../../../../plugins/drop_folder/config/drop_folder_permissions_to_remove.ini';
passthru("php $script $config");

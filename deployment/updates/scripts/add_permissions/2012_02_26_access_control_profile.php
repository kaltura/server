<?php
/**
 * @package deployment
 * @subpackage eagle.roles_and_permissions
 * 
 * Adds access control service permissions
 * 
 * No need to re-run after server code deploy
 */

$script = realpath(dirname(__FILE__) . '/../../../../') . '/scripts/utils/permissions/addPermissionsAndItems.php';
$config = realpath(dirname(__FILE__)) . '/configs/access_control_profile.ini';
passthru("php $script $config");

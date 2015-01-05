<?php
/**
 * @package deployment
 * @subpackage falcon.roles_and_permissions
 * 
 * Add permission to base_playback role
 */

 
$script = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';
$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.flavorasset.ini';
passthru("php $script $config");
 

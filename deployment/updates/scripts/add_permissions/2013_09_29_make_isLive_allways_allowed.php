<?php
/**
 * @package deployment
 * @subpackage falcon.roles_and_permissions
 * 
 * isLive not require KS
 */

$script = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';
$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.livestream.ini';
passthru("php $script $config");
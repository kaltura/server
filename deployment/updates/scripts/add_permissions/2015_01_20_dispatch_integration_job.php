<?php
/**
 * @package deployment
 * @subpackage jupiter.roles_and_permissions
 * 
 * Enable integration.dispatch to all internal IPs or eCDN partners
 */

 
$script = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';
$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.integration.integration.ini';
passthru("php $script $config");
 

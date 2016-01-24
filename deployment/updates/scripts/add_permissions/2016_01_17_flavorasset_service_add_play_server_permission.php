<?php
/**
 * @package deployment
 * @subpackage falcon.roles_and_permissions
 * 
 * Add play server permission to flavorasset.getUrl
 */

$script = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';

$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.flavorasset.ini';
passthru("php $script $config");

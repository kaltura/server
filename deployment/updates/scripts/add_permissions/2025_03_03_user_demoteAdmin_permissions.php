<?php
/**
 * @package deployment
 * @subpackage ursa.roles_and_permissions
 */

$script = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';

$config = realpath(dirname(__FILE__) . '/../../../') . '/permissions/service.user.ini';
passthru("php $script $config");

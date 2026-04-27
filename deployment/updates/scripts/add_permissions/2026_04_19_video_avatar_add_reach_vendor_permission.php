<?php
/**
 * @package deployment
 * @subpackage Venus.roles_and_permissions
 */

$script = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';
$config = realpath(dirname(__FILE__) . '/../../../') . '/permissions/partner.-27.ini';
passthru("php $script $config");

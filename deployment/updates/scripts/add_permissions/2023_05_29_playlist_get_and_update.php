<?php
/**
 * @package deployment
 * @subpackage scorpius.roles_and_permissions
 *
 * Update playlist update permissions
 */
$script = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';
$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.playlist.ini';
passthru("php $script $config");

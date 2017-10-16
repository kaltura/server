<?php
/**
 * @package deployment
 * @subpackage lynx.roles_and_permissions
 */

$script = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';
$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.poll.poll.ini';
passthru("php $script $config");

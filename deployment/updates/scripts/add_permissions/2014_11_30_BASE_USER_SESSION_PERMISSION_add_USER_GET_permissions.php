<?php
/**
 * @package deployment
 * @subpackage falcon.roles_and_permissions
 *
 * Add user->get permission to BASE_USER_SESSION_PERMISSION permission
 */


$script = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';
$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.user.ini';
passthru("php $script $config");

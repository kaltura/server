<?php
/**
 * @package deployment
 * @subpackage kajam.roles_and_permissions
 *
 * Added permission to adminconsole entryadmin
 */

$script = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';
$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.adminconsole.entryadmin.ini';
passthru("php $script $config");
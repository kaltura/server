<?php
/**
 * @package deployment
 * @subpackage tucana.roles_and_permissions
 */

$script = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';
$config = realpath(dirname(__FILE__)) . '/../../../permissions/partner.0.ini';
passthru("php $script $config");

$config = realpath(dirname(__FILE__) . '/../../../') . '/permissions/service.session.ini';
passthru("php $script $config");

<?php
/**
 * @package deployment
 * @subpackage mercury.roles_and_permissions
 */

$addPermissionsScript = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';

$config = realpath(dirname(__FILE__)) . '/../../../permissions/partner.-1.ini';
passthru("php $script $config");

$addConfig = realpath(dirname(__FILE__)) . '/../../../permissions/service.annotation.annotation.ini';
passthru("php $addPermissionsScript $addConfig");

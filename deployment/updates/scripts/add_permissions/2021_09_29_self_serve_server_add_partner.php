<?php
/**
 * @package deployment
 * @subpackage quasar.roles_and_permissions
 */

$script = realpath(dirname(__FILE__) . '/../../../') . '/base/scripts/insertDefaults.php';
$config = realpath(dirname(__FILE__) . '/../../../') . '/base/scripts/init_data/01.Partner.ini';
passthru("php $script $config");

$script = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';

$config = realpath(dirname(__FILE__) . '/../../../') . '/permissions/partner.-12.ini';
passthru("php $script $config");

$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.systempartner.systempartner.ini';

passthru("php $script $config");

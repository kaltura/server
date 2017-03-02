<?php
/**
 * @package deployment
 * 
 * Add integration->notify permission-item & permission
 */
 
$script = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';
$config = realpath(dirname(__FILE__)) . '/../../../permissions/partner.0.ini';
passthru("php $script $config");

$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.integration.integration.ini';
passthru("php $script $config");

$script = realpath(dirname(__FILE__) . '/../../../') . '/base/scripts/insertDefaults.php';
$config = realpath(dirname(__FILE__) . '/../../../') . '/base/scripts/init_data/03.UserRole.ini';
passthru("php $script $config");

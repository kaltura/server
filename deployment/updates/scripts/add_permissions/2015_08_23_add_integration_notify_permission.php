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

//add user role
$config = realpath(dirname(__FILE__)) . '/../2015_08_23_create_external_services_role.php';
passthru("php $config realrun");

<?php
/**
 * @package deployment
 * @subpackage Naos.roles_and_permissions
 */

$addPermissionsAndItemsScript = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';

$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.elasticsearch.esearch.ini ';
passthru("php $addPermissionsAndItemsScript $config");

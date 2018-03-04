<?php
/**
 * @package deployment
 * @subpackage mercury.roles_and_permissions
 */

$removePermissionsScript = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/removePermissionsAndItems.php';

$removeConfig = realpath(dirname(__FILE__)) . '/../ini_files/2018_02_28_esearch_update_permissions.ini';
passthru("php $removePermissionsScript $removeConfig");


$addPermissionsScript = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';

$addConfig = realpath(dirname(__FILE__)) . '/../../../permissions/service.elasticsearch.esearch.ini';
passthru("php $addPermissionsScript $addConfig");

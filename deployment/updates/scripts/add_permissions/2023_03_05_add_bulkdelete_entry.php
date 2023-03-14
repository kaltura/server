<?php
/**
 * @package deployment
 * @subpackage scorpius.roles_and_permissions
 */

$addPermissionsScript = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';

$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.bulkupload.bulk.ini ';
passthru("php $addPermissionsScript $config");

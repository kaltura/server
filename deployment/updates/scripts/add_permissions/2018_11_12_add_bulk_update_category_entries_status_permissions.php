<?php
/**
 * @package deployment
 * @subpackage naos.roles_and_permissions
 *
 * Update bulk permissions
 */
$script = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';
$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.bulkupload.bulk.ini';
passthru("php $script $config");
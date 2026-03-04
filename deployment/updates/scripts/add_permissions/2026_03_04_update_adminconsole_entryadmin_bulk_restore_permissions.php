<?php
/**
 * @package deployment
 * @subpackage roles_and_permissions
 *
 * Update permission to adminconsole entryadmin - add bulkRestoreDeletedEntries action
 */

$addPermissionsAndItemsScript = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';

$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.adminconsole.entryadmin.ini';
passthru("php $addPermissionsAndItemsScript $config");


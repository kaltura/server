<?php
/**
 * @package deployment
 * Add new permissions to new action extendLockExpiration in batchService
 */

$script = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';
$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.batch.ini';
passthru("php $script $config");

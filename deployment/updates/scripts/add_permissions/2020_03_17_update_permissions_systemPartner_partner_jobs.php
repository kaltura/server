<?php
/**
 * @package deployment
 * Update permissions to systemPartner, jobs services
 */

$addPermissionsAndItemsScript = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';

$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.systempartner.systempartner.ini';
passthru("php $addPermissionsAndItemsScript $config");

$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.jobs.ini';
passthru("php $addPermissionsAndItemsScript $config");
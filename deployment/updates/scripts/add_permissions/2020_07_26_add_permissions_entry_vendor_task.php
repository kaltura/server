<?php
/**
 * @package deployment
 * Add permission REACH_ADMIN_PERMISSION to reach entry vendor task serve and getServeUrl
 */

$script = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';
$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.reach.entryVendorTask.ini';
passthru("php $script $config");
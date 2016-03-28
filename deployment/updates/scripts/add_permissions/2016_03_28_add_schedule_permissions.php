<?php
/**
 * @package deployment
 * 
 * Add like->list permission-item
 */

$script = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';
$config = realpath(dirname(__FILE__)) . '/../../../permissions/partner.0.ini';
passthru("php $script $config");

$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.schedule.scheduleResource.ini';
passthru("php $script $config");
 
$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.schedule.scheduleEvent.ini';
passthru("php $script $config");

$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.schedule.scheduleEventResource.ini';
passthru("php $script $config");

$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.scheduleBulkUpload.scheduleBulk.ini';
passthru("php $script $config");

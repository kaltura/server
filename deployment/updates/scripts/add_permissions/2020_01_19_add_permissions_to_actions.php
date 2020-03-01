<?php
/**
 * @package deployment
 * Add permissions to systemPartner and to jobs
 */

$script = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';
$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.systempartner.systempartner.ini';
passthru("php $script $config");

$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.jobs.ini';
passthru("php $script $config");

$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.partner.ini';
passthru("php $script $config");
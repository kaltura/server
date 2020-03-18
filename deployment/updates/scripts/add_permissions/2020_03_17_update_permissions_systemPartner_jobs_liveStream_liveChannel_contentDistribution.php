<?php
/**
 * @package deployment
 * Update permissions to systemPartner, jobs , livestream, livechannel, contentdistributionbatch services
 */

$addPermissionsAndItemsScript = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';

$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.systempartner.systempartner.ini';
passthru("php $addPermissionsAndItemsScript $config");

$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.jobs.ini';
passthru("php $addPermissionsAndItemsScript $config");

$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.livestream.ini';
passthru("php $addPermissionsAndItemsScript $config");

$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.livechannel.ini';
passthru("php $addPermissionsAndItemsScript $config");

$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.contentdistribution.contentdistributionbatch.ini';
passthru("php $addPermissionsAndItemsScript $config");
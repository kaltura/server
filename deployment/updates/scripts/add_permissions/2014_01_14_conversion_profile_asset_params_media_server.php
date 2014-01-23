<?php
/**
 * @package deployment
 * @subpackage falcon.roles_and_permissions
 * 
 * Add permissions to caption asset
 */

$script = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';


$config = realpath(dirname(__FILE__)) . '/../../../permissions/partner.0.ini';
passthru("php $script $config");

$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.livechannel.ini';
passthru("php $script $config");

$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.livechannelsegment.ini';
passthru("php $script $config");

$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.baseentry.ini';
passthru("php $script $config");

$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.filesync.filesync.ini';
passthru("php $script $config");

$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.flavorasset.ini';
passthru("php $script $config");

$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.flavorparams.ini';
passthru("php $script $config");

$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.playlist.ini';
passthru("php $script $config");

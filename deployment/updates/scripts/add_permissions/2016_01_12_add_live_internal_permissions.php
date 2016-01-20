<?php
/**
 * @package deployment
 * 
 * Add nginxLive_liveInternal.* internal IPs access
 */

$script = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';

$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.nginxLive.liveInternal.ini';
passthru("php $script $config");

$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.servernode.ini';
passthru("php $script $config");

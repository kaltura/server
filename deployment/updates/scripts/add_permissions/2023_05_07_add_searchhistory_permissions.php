<?php
/**
 * @package deployment
 * @subpackage Scorpius.roles_and_permissions
 */

$script = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';

$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.searchhistory.searchhistory.ini';
passthru("php $script $config");

$config = realpath(dirname(__FILE__)) . '/../../../permissions/object.KalturaESearchHistoryFilter.ini';
passthru("php $script $config");
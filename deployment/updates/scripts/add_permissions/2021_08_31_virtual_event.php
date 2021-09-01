<?php
/**
 * @package deployment
 */

$addPermissionsAndItemsScript = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';

$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.virtualevent.ini';
passthru("php $addPermissionsAndItemsScript $config");

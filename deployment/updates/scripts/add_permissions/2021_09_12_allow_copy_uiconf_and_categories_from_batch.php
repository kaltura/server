<?php
/**
 * @package deployment
 */

$addPermissionsScript = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';

$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.uiconf.ini';
passthru("php $addPermissionsScript $config");

$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.category.ini';
passthru("php $addPermissionsScript $config");

